<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;

class EngineService
{
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

    public function logEmail(array $data, string $status, ?string $errorMessage, int $applicationId = null)
    {
        EmailLog::create([
            'application_id' => $applicationId,
            'request' => json_encode($data),
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'success' ? now() : null,
        ]);
    }
}