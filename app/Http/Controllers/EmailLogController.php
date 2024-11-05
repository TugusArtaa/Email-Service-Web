<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailLogController extends Controller
{
    public function index()
    {
        // Ambil semua data EmailLog dan relasi dengan tabel application
        $emailLogs = EmailLog::with('application')
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($emailLogs);

        // Render halaman 'Home' menggunakan Inertia.js dan mengirimkan data emailLogs sebagai 'data'
        return Inertia::render('Home', [
            'data' => $emailLogs,
        ]);
    }
}