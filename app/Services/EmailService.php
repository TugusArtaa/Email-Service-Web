<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendEmail(array $data)
    {
        Mail::raw($data['content'], function ($message) use ($data) {
            $message->to($data['to'])
                    ->subject($data['subject']);
            if (!empty($data['attachment'])) {
                foreach ($data['attachment'] as $attachment) {
                    $message->attach($attachment);
                }
            }
        });
    }
}
