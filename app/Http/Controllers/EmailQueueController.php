<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Http\Requests\ExtractEmailRequest;
use App\Http\Requests\ExcelRequest;
use App\Services\EmailQueueService;
use App\Models\Application;
use App\Services\EmailLogService;

class EmailQueueController extends Controller
{
    private $emailService;

    //Constructor untuk menginisialisasi service 
    public function __construct(EmailQueueService $emailService)
    {
        $this->emailService = $emailService;
    }

    //Function untuk mengirim email ke dalam queue RabbitMQ
    public function sendEmails(SendEmailRequest $request)
    {
        $data = $request->json()->all();

        // Ambiil aplikasi berdasarkan secret key
        $application = Application::where('secret_key', $data['secret'])->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Secret key tidak valid',
            ], 422);
        }

        if ($application->status !== 'enabled') {
            return response()->json([
                'success' => false,
                'message' => 'Status aplikasi disabled',
            ], 422);
        }

        usort($data['mail'], function ($a, $b) {
            $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
            return $priorityMap[$b['priority']] <=> $priorityMap[$a['priority']];
        });

        $messages = [];
        foreach ($data['mail'] as $mail) {
            // Buat entry log dengan status 'pending'
            $emailLog = app(EmailLogService::class)->logEmail($mail, $application->id);
            $mail['id'] = $emailLog->id;
            $messages[] = $mail;
        }

        // Email di proses dan dimasukkan ke dalam antrian RabbitMQ
        $result = $this->emailService->processAndQueueEmails($messages, $data['secret']);
        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email masuk kedalam antrian',
            'messages' => $result['messages'],
        ]);
    }

    //Function untuk mengirim email dari file excel ke dalam queue RabbitMQ
    public function sendEmailsFromExcel(ExcelRequest $request)
    {
        $file = $request->file('excel_file');

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diunggah',
            ], 400);
        }

        $result = $this->emailService->processEmailsFromExcel($file);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 400);
        }

        if (isset($result['validationErrors'])) {
            return response()->json([
                'success' => false,
                'messages' => $result['validationErrors'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email dari file excel masuk kedalam antrian',
            'messages' => $result['messages'],
        ]);
    }

    //Function untuk mengambil data email log berdasarkan id
    public function extractEmailData(ExtractEmailRequest $request)
    {
        $data = $this->emailService->extractEmailLogData($request);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}