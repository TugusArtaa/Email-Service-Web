<?php

namespace App\Services;

use App\Models\Application;
use PhpAmqpLib\Message\AMQPMessage;
use App\Models\EmailLog;

class EmailQueueService
{
    private $channel;

    public function __construct(RabbitMQService $RabbitMQService)
    {
        $this->channel = $RabbitMQService->connect();
    }

//Method untuk memproses dan memasukan email ke dalam queue
    public function processAndQueueEmails(array $emails, string $secret)
    {
        $application = Application::where('secret_key', $secret)->first();
        if (!$application) {
            return ['error' => 'Invalid secret key'];
        }

        $messages = [];
        foreach ($emails as $mail) {
            try {
                // Get the priority from the mail data or set it to 3 (medium) by default
                $priority = $mail['priority'] ?? 3;

                // Ensure priority is between 1 and 20
                $priority = min(max($priority, 1), 20); 

                $messageData = [
                    'to' => $mail['to'],
                    'subject' => $mail['subject'],
                    'content' => $mail['content'],
                    'attachment' => $mail['attachment'] ?? [],
                    'priority' => $priority,
                    'secret' => $secret 
                ];

                // Create a message with the priority value
                $msg = new AMQPMessage(
                    json_encode($messageData),
                    [
                        'delivery_mode' => 2, // Make message persistent
                        'priority' => $priority // Set priority here directly
                    ]
                );

                // Publish the message to the single queue with the routing key 'email'
                $this->channel->basic_publish($msg, 'email_exchange', 'email');
                $messages[] = array_merge($messageData, ['priority' => $priority]);
            } catch (\Exception $e) {
                return ['error' => 'Queue error: ' . $e->getMessage()];
            }
        }

        return ['messages' => $messages];
    }

//Method untuk mengekstrak data email log berdasarkan ID
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

//Method untuk mengekstrak data json dari email log dalam databse
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
}

