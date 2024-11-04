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

    public function sendEmails(SendEmailRequest $request)
    {
        $data = $request->json()->all();
        $result = $this->emailService->processAndQueueEmails($data['mail'], $data['secret']);
        
        if (isset($result['error'])) {
            return errorResponse($result['error']['message'], 422);
        }
        
        return responseWithData('Email message(s) sent to queue', $result['messages']);
    }

    public function extractEmailData($id)
    {
        return $this->emailService->extractEmailLogData($id);
    }  

    public function extractAllEmailData()
    {
        return $this->emailService->extractAllEmailLogData();
    }
}

