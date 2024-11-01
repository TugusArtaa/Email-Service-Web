<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RabbitMQController extends Controller
{
    private $connection;
    private $channel;

    public function __construct()
    {
        // Initialize RabbitMQ connection and channel
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();

        // Declare the exchange for routing emails
        $this->channel->exchange_declare('email_exchange', 'topic', false, true, false);

        // Set maximum priority level
        $maxPriority = 20;

        // Declare classic queues with priority support
        $this->channel->queue_declare('email_high_priority', false, true, false, false, false, [
            'x-max-priority' => ['I', $maxPriority]
        ]);

        $this->channel->queue_declare('email_medium_priority', false, true, false, false, false, [
            'x-max-priority' => ['I', $maxPriority]
        ]);

        $this->channel->queue_declare('email_low_priority', false, true, false, false, false, [
            'x-max-priority' => ['I', $maxPriority]
        ]);

        // Bind queues to the exchange with the updated routing keys
        $this->channel->queue_bind('email_high_priority', 'email_exchange', 'email.high_priority');
        $this->channel->queue_bind('email_medium_priority', 'email_exchange', 'email.medium_priority');
        $this->channel->queue_bind('email_low_priority', 'email_exchange', 'email.low_priority');
    }

    public function sendToQueue(Request $request)
    {
        $messages = [];

        foreach ($request->input('mail', []) as $mail) {
            // Extract priority directly from the request data, defaulting to 3 if not provided
            $priority = $mail['priority'] ?? 3;

            // Determine the correct queue and routing key based on priority
            $routingKey = match (true) {
                $priority >= 15 => 'email.high_priority',
                $priority >= 8 => 'email.medium_priority',
                default => 'email.low_priority'
            };

            // Create the AMQP message with priority
            $msg = new AMQPMessage(json_encode([
                'to' => $mail['to'],
                'subject' => $mail['subject'],
                'content' => $mail['content'],
                'attachment' => $mail['attachment'] ?? []
            ]), [
                'delivery_mode' => 2, // Persistent messages
                'priority' => $priority // Set priority for the message
            ]);

            // Publish the message to the exchange with the routing key
            $this->channel->basic_publish($msg, 'email_exchange', $routingKey);

            $messages[] = [
                'to' => $mail['to'],
                'subject' => $mail['subject'],
                'content' => $mail['content'],
                'attachment' => $mail['attachment'] ?? [],
                'priority' => $priority
            ];
        }

        return response()->json([
            'message' => 'Email message(s) sent to queue with dynamic priority',
            'data' => $messages
        ], 200);
    }

    public function sendEmail()
    {
        // Declare and consume from each queue
        $queues = ['email_high_priority', 'email_low_priority', 'email_medium_priority'];
        foreach ($queues as $queue) {
            $callback = function (AMQPMessage $msg) {
                $data = json_decode($msg->body, true);

                // Initialize retry count if not present
                $retryCount = isset($data['retry_count']) ? $data['retry_count'] : 0;

                // Validate that data contains the expected structure
                $validator = Validator::make($data, [
                    'to' => 'required|email',
                    'subject' => 'required|string',
                    'content' => 'required|string',
                    'attachment' => 'array',
                    'attachment.*' => 'url',
                ]);

                // If validation fails, log the error and reject the message
                if ($validator->fails()) {
                    \Log::error('Validation failed for message: ' . json_encode($validator->errors()));
                    // Acknowledge without requeuing
                    $this->channel->basic_ack($msg->delivery_info['delivery_tag']);
                    return;
                }

                try {
                    // Send email with raw content
                    Mail::send([], [], function ($message) use ($data) {
                        $message->to($data['to'])
                                ->subject($data['subject'])
                                ->html($data['content']);
                
                        // Handle attachments if any
                        if (!empty($data['attachment'])) {
                            foreach ($data['attachment'] as $attachment) {
                                // Ensure each attachment is added correctly
                                $message->attach($attachment);
                            }
                        }
                    });
                    
                    \Log::info("Email sent to {$data['to']} with subject: {$data['subject']}");
                    // Acknowledge the message after successful processing
                    $this->channel->basic_ack($msg->delivery_info['delivery_tag']);
                } catch (\Exception $e) {
                    // Log the exception
                    \Log::error('Failed to send email: ' . $e->getMessage());
                
                    // Increment the retry count
                    $retryCount++;
                
                    // Check if retry count exceeds the threshold
                    if ($retryCount > 1) {
                        // Acknowledge without requeuing if max retries reached
                        \Log::error('Max retry limit reached for message: ' . json_encode($data));
                        $this->channel->basic_ack($msg->delivery_info['delivery_tag']);
                    } else {
                        // Update retry count and requeue the message
                        $data['retry_count'] = $retryCount;
                        $msg->body = json_encode($data); // Update the message body
                        $this->channel->basic_nack($msg->delivery_info['delivery_tag'], false, false); // Nack without requeue
                        $this->channel->basic_publish($msg, '', $msg->delivery_info['routing_key']); // Requeue
                    }
                }
            };

            // Consume messages from the specified queue
            $this->channel->basic_consume($queue, '', false, false, false, false, $callback);
        }

        // Keep consuming messages until stopped
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
    
}
