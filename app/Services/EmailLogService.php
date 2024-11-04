<?php

namespace App\Services;

use App\Models\EmailLog;

class EmailLogService
{
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
