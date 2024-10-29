<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQController extends Controller
{
    private $connection;
    private $channel;

    public function __construct()
    {
        // Initialize RabbitMQ connection and channel only
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
    }

    public function send(Request $request)
    {
        $messages = [];

        foreach ($request->input('mail', []) as $mail) {
            $priority = $this->determinePriority($mail);
            $routingKey = $this->getRoutingKeyForPriority($priority);

            // Create the AMQP message
            $msg = new AMQPMessage(json_encode([
                'to' => $mail['to'],
                'subject' => $mail['subject'],
                'content' => $mail['content'],
                'attachment' => $mail['attachment'] ?? []
            ]), [
                'delivery_mode' => 2, // Persistent messages
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

    private function determinePriority(array $mail): int
    {
        if (str_contains($mail['content'], 'urgent') || str_contains($mail['subject'], 'high priority')) {
            return 10;
        } elseif (str_contains($mail['content'], 'important')) {
            return 7;
        } else {
            return 3;
        }
    }

    private function getRoutingKeyForPriority(int $priority): string
    {
        return match (true) {
            $priority >= 10 => 'email.urgent',
            $priority >= 7 => 'email.transactional',
            default => 'email.marketing'
        };
    }
}
