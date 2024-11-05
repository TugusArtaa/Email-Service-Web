<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQService
{
    private $connection;
    private $channel;
    
    public function __construct()
    {
        $this->connect();
        $this->initializeEmailQueue();
    }

    public function connect()
    {
        if (!$this->connection) {
            $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $this->channel = $this->connection->channel();
        }
        return $this->channel;
    }

    public function initializeEmailQueue()
    {
        try {
            // Declare the exchange for routing emails
            $this->channel->exchange_declare('email_exchange', 'topic', false, true, false);
            // Set maximum priority level
            $maxPriority = 20;
            // Declare queues with priority support
            $this->channel->queue_declare('email_high_priority', false, true, false, false, false, [
                'x-max-priority' => ['I', $maxPriority]
            ]);

            $this->channel->queue_declare('email_medium_priority', false, true, false, false, false, [
                'x-max-priority' => ['I', $maxPriority]
            ]);

            $this->channel->queue_declare('email_low_priority', false, true, false, false, false, [
                'x-max-priority' => ['I', $maxPriority]
            ]);

            // Bind queues to the exchange
            $this->channel->queue_bind('email_high_priority', 'email_exchange', 'email.high_priority');
            $this->channel->queue_bind('email_medium_priority', 'email_exchange', 'email.medium_priority');
            $this->channel->queue_bind('email_low_priority', 'email_exchange', 'email.low_priority');
        } catch (\Exception $e) {
            return ['error' => 'Connection error: ' . $e->getMessage()];
        }
    }

    public function consume(string $queue, callable $callback)
    {
        $this->channel->basic_consume($queue, '', false, true, false, false, $callback);
    }

    public function wait()
    {
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}