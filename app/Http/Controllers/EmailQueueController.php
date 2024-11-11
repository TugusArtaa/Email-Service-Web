<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Services\EmailQueueService;

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
        
        if (isset($result['error'])) {
            return errorResponse($result['error']['message'], 422);
        }
        $result = $this->emailService->processAndQueueEmails($data['mail'], $data['secret']);
        if (isset($result['error'])) {
            return errorResponse($result['error'], 422);
        }
        
        return responseWithData('Email message(s) sent to queue', $result['messages']);
    }
//Method untuk mengambil data email log berdasarkan id
    public function extractEmailData($id)
    {
        return $this->emailService->extractEmailLogData($id);
    }  
//Method untuk mengambil semua data email log
    public function extractAllEmailData()
    {
        return $this->emailService->extractAllEmailLogData();
    }
}

