<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Log;

class EmailLogService
{
    public function getAllEmailLogs()
    {
        return EmailLog::with('application')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getEmailLogs($perPage = 10, $search = null, $orderBy = 'desc')
    {
        $query = EmailLog::with('application');

        if ($search) {
            $query->where('status', 'like', '%' . $search . '%');
        }

        return $query->orderBy('created_at', $orderBy)
            ->paginate($perPage);
    }

    public function deleteEmailLogs($startDate, $endDate)
    {
        $query = EmailLog::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query->delete();
    }

    public function deleteEmailLogsByIds(array $ids)
    {
        return EmailLog::whereIn('id', $ids)->delete();
    }

    // Method untuk menyimpan log email ke dalam databse
    public function logEmail(array $data, int $applicationId)
    {
        // Create the email log entry with default pending status
        $emailLog = EmailLog::create([
            'application_id' => $applicationId,
            'request' => json_encode($data),
            'status' => 'pending', // Default status
            'error_message' => null, // No error initially
            'sent_at' => null, // Not sent yet
        ]);

        // Return the created log entry
        return $emailLog;
    }

    public function updateLog(array $data, string $status, ?string $errorMessage, int $applicationId)
    {
        // Find the existing email log by ID
        $emailLog = EmailLog::find($data['id']);

        if ($emailLog) {
            // Ensure we are updating the request field with the latest data
            $updatedEmailData = $data['mail'] ?? $data;

            unset($updatedEmailData['id']); // Remove the ID from the updated data

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
                'id' => $emailLog->id,
                'application_id' => $applicationId,
                'request' => $updatedEmailData,
                'status' => $status,
                'error_message' => $errorMessage,
                'sent_at' => $status === 'success' ? now() : null,
            ];

            // Log to a specific channel (email) for Elasticsearch
            Log::channel('email')->info('Email log created', $logMessage);
        }
    }
}