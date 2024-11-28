<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Http\Requests\ExcelRequest;
use App\Services\EmailQueueService;
use App\Models\Application;
use App\Services\EmailLogService;

class EmailQueueController extends Controller
{
    private $emailService;
    public function __construct(EmailQueueService $emailService)
    {
        $this->emailService = $emailService;
    }

    //Method untuk mengirim email ke dalam queue RabbitMQ
    public function sendEmails(SendEmailRequest $request)
    {
        $data = $request->json()->all();

        // Retrieve the application based on the secret key and check status
        $application = Application::where('secret_key', $data['secret'])
            ->where('status', 'enabled')
            ->first();

        if (!$application) {
            return errorResponse('Invalid secret key', 422);
        }

        // Sort the emails by priority
        usort($data['mail'], function ($a, $b) {
            $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
            return $priorityMap[$b['priority']] <=> $priorityMap[$a['priority']];
        });

        $messages = [];
        foreach ($data['mail'] as $mail) {
            // Create the log entry with pending status
            $emailLog = app(EmailLogService::class)->logEmail($mail, $application->id);
            $mail['id'] = $emailLog->id; // Add the log ID to the message data
            $messages[] = $mail;
        }

        // Process and queue the emails
        $result = $this->emailService->processAndQueueEmails($messages, $data['secret']);
        if (isset($result['error'])) {
            return errorResponse($result['error'], 422);
        }

        return responseWithData('Email message(s) sent to queue', $result['messages']);
    }
    
    //Method untuk mengirim email dari file excel ke dalam queue RabbitMQ
    public function sendEmailsFromExcel(ExcelRequest $request)
    {
        $file = $request->file('excel_file');

        if (!$file) {
            return errorResponse('No file uploaded', 400); // Using errorResponse helper
        }

        $result = $this->emailService->processEmailsFromExcel($file);

        if (isset($result['error'])) {
            return errorResponse($result['error'], 400); // Using errorResponse helper
        }

        if (isset($result['validationErrors'])) {
            return validationError($result['validationErrors']); // Using validationError helper
        }

        return queueSuccess($result['messages']); // Using queueSuccess helper
    }

    //Method untuk mengambil data email log berdasarkan id
    public function extractEmailData(SendEmailRequest $request)
    {
        return $this->emailService->extractEmailLogData($request);
    }
}

