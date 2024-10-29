<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailEngine extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ and send emails';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Connect to RabbitMQ
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // Declare and consume from each queue
        $queues = ['email_urgent', 'email_transactional', 'email_marketing'];
        foreach ($queues as $queue) {
            $callback = function (AMQPMessage $msg) use ($channel) {
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
                    $this->error('Validation failed for message: ' . json_encode($validator->errors()));
                    // Acknowledge without requeuing
                    $channel->basic_ack($msg->delivery_info['delivery_tag']);
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
                    
                    $this->info("Email sent to {$data['to']} with subject: {$data['subject']}");
                    // Acknowledge the message after successful processing
                    $channel->basic_ack($msg->delivery_info['delivery_tag']);
                } catch (\Exception $e) {
                    // Log the exception
                    $this->error('Failed to send email: ' . $e->getMessage());
                
                    // Increment the retry count
                    $retryCount++;
                
                    // Check if retry count exceeds the threshold
                    if ($retryCount > 1) {
                        // Acknowledge without requeuing if max retries reached
                        $this->error('Max retry limit reached for message: ' . json_encode($data));
                        $channel->basic_ack($msg->delivery_info['delivery_tag']);
                    } else {
                        // Update retry count and requeue the message
                        $data['retry_count'] = $retryCount;
                        $msg->body = json_encode($data); // Update the message body
                        $channel->basic_nack($msg->delivery_info['delivery_tag'], false, false); // Nack without requeue
                        $channel->basic_publish($msg, '', $msg->delivery_info['routing_key']); // Requeue
                    }
                }
            };

            // Consume messages from the specified queue
            $channel->basic_consume($queue, '', false, false, false, false, $callback);
        }

        // Keep consuming messages until stopped
        while ($channel->is_consuming()) {
            $channel->wait();
        }

        // Close connection when done
        $channel->close();
        $connection->close();
    }
}
