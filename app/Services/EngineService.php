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
        // Simpan log ke database
        EmailLog::create([
            'application_id' => $applicationId,
            'request' => json_encode($data),
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'success' ? now() : null,
        ]);

        // Simpan log ke file log menggunakan channel 'email'
        $logMessage = [
            'application_id' => $applicationId,
            'request' => $data,
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'success' ? now() : null,
        ];
        Log::channel('email')->info('Email log', $logMessage);
    }
}