<?php

namespace App\Http\Controllers;

use App\Http\Requests\RetryEmailRequest;
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
        $data = $request->validated();
        
        if (isset($result['error'])) {
            return errorResponse($result['error']['message'], 422);
        }
        $result = $this->emailService->processAndQueueEmails($data['mail'], $data['secret']);
        if (isset($result['error'])) {
            return errorResponse($result['error'], 422);
        }

        // return redirect()->back()->with('success', 'Email message(s) sent to queue');
        return responseWithData('Email message(s) sent to queue', $result['messages']);
    }

    //Method untuk mencoba ulang mengirim email yang gagal
    public function retryEmails(RetryEmailRequest $request)
    {
        $data = $request->json()->all();
        
        if (empty($data['id'])) {
            return redirect()->back()->with('error', 'Email log ID required.');
            // return errorResponse('Email log ID required.', 422);
        }
    
        // Ensure the mail data is included in the request
        if (empty($data['mail']) || !is_array($data['mail'])) {
            return redirect()->back()->with('error', 'Email data is required to resend.');
            // return errorResponse('Email data is required to resend.', 422);
        }
    
        // Pass the entire $data array (id and mail) for retrying
        $result = $this->emailService->retryFailedEmails($data);
    
        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
            // return errorResponse($result['error'], 422);
        }
        
        return redirect()->back()->with('success', 'Failed email retried successfully.');
        // return responseWithData('Failed email retried successfully.', $result);
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

