<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Log;

class EmailLogService
{
    // Function untuk mengambil semua log email
    public function getAllEmailLogs()
    {
        return EmailLog::with('application')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function countByStatus($status)
    {
        return EmailLog::where('status', $status)->count();
    }

    public function countAll()
    {
        return EmailLog::count();
    }

    // Function untuk mengambil log email dengan pagination, pencarian, dan pengurutan
    public function getEmailLogs($perPage = 10, $search = null, $orderBy = 'desc')
    {
        $query = EmailLog::with('application');

        // Filter berdasarkan parameter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('status', 'like', '%' . $search . '%')
                    ->orWhereHas('application', function ($appQuery) use ($search) {
                        $appQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('request', 'like', '%' . $search . '%');
            });
        }

        // Urutkan berdasarkan parameter orderBy
        return $query->orderBy('created_at', $orderBy)
            ->paginate($perPage);
    }

    // Function untuk menghapus log email
    public function deleteEmailLogsByIds(array $ids): int
    {
        return EmailLog::whereIn('id', $ids)->delete();
    }

    // Function untuk menyimpan log email pending ketika email akan dikirim
    public function logEmail(array $data, int $applicationId): EmailLog
    {
        // Buat log email sementara dengan status 'pending'
        $emailLog = EmailLog::create([
            'application_id' => $applicationId,
            'request' => json_encode($data),
            'status' => 'pending', // Default status
            'error_message' => null, // No error initially
            'sent_at' => null, // Not sent yet
        ]);

        return $emailLog;
    }

    // Function untuk memperbarui log email setelah pengiriman dilakukan
    public function updateLog(array $data, string $status, ?string $errorMessage, int $applicationId)
    {
        // Find the existing email log by ID
        $emailLog = EmailLog::find($data['id']);
        if (!$emailLog) {
            Log::error("Log dengan id {$data['id']} tidak ditemukan.");
            return;
        }

        if ($emailLog) {
            $updatedEmailData = $data['mail'] ?? $data;
            unset($updatedEmailData['id']); // hapus id dari data yang akan disimpan

            // Jika status adalah 'success', set sent_at ke waktu saat ini
            if ($status === 'success') {
                $errorMessage = null;
                $emailLog->sent_at = now();
            }

            $emailLog->status = $status;
            $emailLog->error_message = $errorMessage;
            $emailLog->request = json_encode($updatedEmailData);
            $emailLog->save();

            $logMessage = [
                'id' => $emailLog->id,
                'application_id' => $applicationId,
                'request' => $updatedEmailData,
                'status' => $status,
                'error_message' => $errorMessage,
                'sent_at' => $status === 'success' ? now() : null,
            ];

            // Log ke dalam direktori log laravel untuk kebutuhan elastic search
            Log::channel('email')->info('Log email dibuat', $logMessage);
        }
    }
}