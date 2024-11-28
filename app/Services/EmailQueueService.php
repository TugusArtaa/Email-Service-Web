<?php

namespace App\Services;

use App\Http\Requests\SendEmailRequest;
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
                $this->channel->basic_publish($msg, 'email', 'email');
                $messages[] = array_merge($messageData, ['priority' => $priority]);
            } catch (\Exception $e) {
                return ['error' => 'Queue error: ' . $e->getMessage()];
            }
        }

        return ['messages' => $messages];
    }

    //Method untuk mencoba ulang mengirim email yang gagal
    public function retryFailedEmails(array $payload)
    {
        $emailLogId = $payload['id'] ?? null;
        if (!$emailLogId) {
            return ['error' => 'Email log ID is required.'];
        }
    
        // Retrieve the email log from the database
        $emailLog = EmailLog::find($emailLogId);
        if (!$emailLog || $emailLog->status !== 'failed') {
            return ['error' => 'Invalid email log ID or the email is not marked as failed.'];
        }
    
        // Decode the original email data from the log (stored request)
        $emailData = json_decode($emailLog->request, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format in email log data.'];
        }
    
        // Now, we want to use the new email data from the payload, overriding the stored data
        $newMailData = $payload['mail'][0] ?? null;
        if ($newMailData) {
            // Fully replace the email data with the new one
            $emailData = $newMailData;
        }
    
        // Ensure the application ID is still correct (retrieve using email log's application ID)
        $application = Application::find($emailLog->application_id);
        if (!$application) {
            return ['error' => 'Application not found for the stored application ID.'];
        }
    
        // Add the correct secret key
        $emailData['secret'] = $application->secret_key;
    
        // Add the email log ID to the message data for retry
        $emailData['id'] = $emailLog->id;
    
        // Retry logic: process and requeue the email with the updated data
        try {
            // Extract priority from the email data, default to 3 if not provided
            $priority = $emailData['priority'] ?? 3;
            $priority = min(max($priority, 1), 20); // Ensure priority is between 1 and 20
    
            // Prepare the message for RabbitMQ
            $msg = new AMQPMessage(
                json_encode($emailData),
                [
                    'delivery_mode' => 2, // Persistent
                    'priority' => $priority,
                ]
            );
    
            // Publish the message to RabbitMQ
            $this->channel->basic_publish($msg, 'email', 'email');
    
            return ['messages' => [$emailData]];
        } catch (\Exception $e) {
            return ['error' => 'Retry error: ' . $e->getMessage()];
        }
    }    

    //Method untuk mengekstrak data email log berdasarkan ID
    public function extractEmailLogData(SendEmailRequest $request)
    {
        $id = $request->input('id');
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
                'secret' => $emailData['secret'] ?? '',
                'status' => $emailLog['status'] ?? '',
                'error_message' => $emailLog['error_message'] ?? ''
            ]);
        } catch (\Exception $e) {
            return queueError('Error processing email log data.');
        }
    }

}

