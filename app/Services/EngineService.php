<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EngineService
{
    // Method untuk mengirim email yang dikonsumsi dari RabbitMQ
    public function sendEmail(array $data)
    {
        Mail::raw($data['content'], function ($message) use ($data) {
            $message->to($data['to'])
                    ->subject($data['subject']);
            if (!empty($data['attachment'])) {
                foreach ($data['attachment'] as $attachment) {
                    $message->attach($attachment);
                }
            }
        });
    }

    // Method untuk menyimpan log email ke dalam databse
    public function logEmail(array $data, string $status, ?string $errorMessage, int $applicationId = null)
    {
        // Create the email log entry in the database and retrieve the created log object
        $emailLog = EmailLog::create([
            'application_id' => $applicationId,
            'request' => json_encode($data),
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'success' ? now() : null,
        ]);

        // Include the auto-incremented id in the log message
        $logMessage = [
            'id' => $emailLog->id,  // Store the auto-incremented ID from the database
            'application_id' => $applicationId,
            'request' => $data,
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'success' ? now() : null,
        ];

        // Log to the Laravel log file (for Filebeat/Logstash to send to Elasticsearch)
        Log::channel('email')->info('Email log created', $logMessage);
    }

    // Method untuk memperbarui log email yang sudah ada
    public function updateLog(array $data, string $status, ?string $errorMessage, int $applicationId)
    {
        // Find the existing email log by ID
        $emailLog = EmailLog::find($data['id']);
    
        if ($emailLog) {
            // Ensure we are updating the request field with the latest data
            $updatedEmailData = $data['mail'] ?? $data;
    
            // If status is 'success', clear the error_message and set sent_at
            if ($status === 'success') {
                $errorMessage = null;  // Remove error_message on success
                $emailLog->sent_at = now(); // Set sent_at to current time if successful
            }
    
            // Update the email log status, error_message, and request field
            $emailLog->status = $status;
            $emailLog->error_message = $errorMessage;
            $emailLog->request = json_encode($updatedEmailData); // Store the latest JSON request
    
            // Save the updated email log
            $emailLog->save();
    
            // Log the update action with the same structure as the initial log
            $logMessage = [
                'id' => $emailLog->id,  // Store the auto-incremented ID from the database
                'application_id' => $applicationId,
                'request' => $updatedEmailData,
                'status' => $status,
                'error_message' => $errorMessage,
                'sent_at' => $status === 'success' ? now() : null,
            ];
    
            // Log to the Laravel log file (for Filebeat/Logstash to send to Elasticsearch)
            Log::channel('email')->info('Email log updated', $logMessage);
        } else {
            // Log an error if the email log does not exist
            Log::channel('email')->error('Email log ID not found', [
                'id' => $data['id'],
            ]);
        }
    }
    
}