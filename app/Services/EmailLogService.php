<?php

namespace App\Services;

use App\Models\EmailLog;

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
}