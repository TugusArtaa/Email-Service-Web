<?php

namespace App\Services;

use App\Models\Application;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Models\EmailLog;

class EmailQueueService
{
    private $connection;
    private $channel;

    public function __construct()
    {
        try {
            // Initialize RabbitMQ connection and channel
            $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $this->channel = $this->connection->channel();

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

    public function processAndQueueEmails(array $emails, string $secret)
    {
        $application = Application::where('secret_key', $secret)->first();
        if (!$application) {
            return ['error' => 'Invalid secret key'];
        }

        $messages = [];
        foreach ($emails as $mail) {
            try {
                $priority = $mail['priority'] ?? 3;
                $routingKey = $this->getRoutingKey($priority);

                $messageData = [
                    'to' => $mail['to'],
                    'subject' => $mail['subject'],
                    'content' => $mail['content'],
                    'attachment' => $mail['attachment'] ?? [],
                    'priority' => $priority,
                    'secret' => $secret 
                ];

                $msg = new AMQPMessage(
                    json_encode($messageData),
                    [
                        'delivery_mode' => 2, 
                    ]
                );

                $this->channel->basic_publish($msg, 'email_exchange', $routingKey);
                $messages[] = array_merge($messageData, ['priority' => $priority]);
            } catch (\Exception $e) {
                return ['error' => 'Queue error: ' . $e->getMessage()];
            }
        }

        return ['messages' => $messages];
    }

    public function extractEmailLogData($id)
    {
        $emailLog = EmailLog::find($id);
    
        if (!$emailLog) {
            return errorResponse('Email log entry not found.', 404);
        }
    
        try {
            $emailData = json_decode($emailLog->request, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                return validationError(['Invalid JSON format in email log data.']);
            }
    
            return responseWithData('Email log data extracted successfully', [
                'to' => $emailData['to'] ?? '',
                'subject' => $emailData['subject'] ?? '',
                'content' => $emailData['content'] ?? '',
                'priority' => $emailData['priority'] ?? '',
                'attachment' => $emailData['attachment'] ?? [],
                'secret' => $emailData['secret'] ?? ''
            ]);
        } catch (\Exception $e) {
            return queueError('Error processing email log data.');
        }
    }

    public function extractAllEmailLogData()
    {
        try {
            $emailLogs = EmailLog::all();
            $extractedData = [];
    
            foreach ($emailLogs as $emailLog) {
                $emailData = json_decode($emailLog->request, true);
    
                // Periksa apakah JSON valid
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return validationError(['Invalid JSON format in email log data.']);
                }
    
                // Simpan data yang diekstrak
                $extractedData[] = [
                    'to' => $emailData['to'] ?? '',
                    'subject' => $emailData['subject'] ?? '',
                    'content' => $emailData['content'] ?? '',
                    'priority' => $emailData['priority'] ?? '',
                    'attachment' => $emailData['attachment'] ?? [],
                    'secret' => $emailData['secret'] ?? ''
                ];
            }
    
            return responseWithData('Email log data extracted successfully', $extractedData);
    
        } catch (\Exception $e) {
            return queueError('Error processing email log data.');
        }
    }
        
    private function getRoutingKey(int $priority): string
    {
        return match (true) {
            $priority >= 15 => 'email.high_priority',
            $priority >= 8 => 'email.medium_priority',
            default => 'email.low_priority'
        };
    }
}
