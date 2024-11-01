<?php 

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
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
        $data = $request->json()->all();
        
        // Validasi dengan custom message
        $validator = Validator::make($data, [
            'secret' => 'required|string',
            'mail' => 'required|array',
            'mail.*.to' => 'required|email',
            'mail.*.content' => 'required|string',
            'mail.*.subject' => 'required|string',
            'mail.*.priority' => 'required|integer|between:1,20',
            'mail.*.attachment' => 'nullable|array',
            'mail..attachment.' => 'nullable|url'
        ], [
            'mail.*.priority.between' => 'Priority harus antara 1-20',
            'mail.*.to.email' => 'Format email tidak valid',
            'mail..attachment..url' => 'Attachment harus berupa URL valid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid request',
                'messages' => $validator->errors()
            ], 422);
        }

        // Validasi secret
        $application = Application::where('secret_key', $data['secret'])->first();
        if (!$application) {
            return response()->json([
                'error' => 'Invalid secret key'
            ], 403);
        }
        
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
}