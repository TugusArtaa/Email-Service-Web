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

    //Untuk koneksi ke RabbitMQ
    public function connect()
    {
        if (!$this->connection) {
            $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $this->channel = $this->connection->channel();
        }
        return $this->channel;
    }

//Untuk menginisialisasi exchange dan queue di RabbitMQ
    public function initializeEmailQueue()
    {
        try {
            // Declare the exchange as 'direct' for routing emails
            $this->channel->exchange_declare('email', 'direct', false, true, false);

            // Declare the queue with priority support (dynamic priority from 1 to 20)
            $this->channel->queue_declare('email_queue', false, true, false, false, false, [
                'x-max-priority' => ['I', 20] // Priority range from 1 to 20
            ]);

            // Bind the queue to the exchange with the routing key 'email'
            $this->channel->queue_bind('email_queue', 'email', 'email');
        } catch (\Exception $e) {
            dd($e);
            return ['error' => 'Connection error: ' . $e->getMessage()];
        }
    }

//Untuk mengonsumsi pesan dari RabbitMQ
    public function consume(string $queue, callable $callback)
    {
        $this->channel->basic_consume($queue, '', false, true, false, false, $callback);
    }

//Sebagai mekanisme agar kode menunggu jika ada message yang masuk di queue
    public function wait()
    {
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

//Untuk menutup koneksi ke RabbitMQ
    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
