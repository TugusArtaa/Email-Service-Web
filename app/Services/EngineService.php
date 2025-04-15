<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EngineService
{
    // Method to send email consumed from RabbitMQ
    public function sendEmail(array $data)
    {
        // Get content and subject from data and handle null or empty cases
        $content = $data['content'] ?? ''; 
        $subject = $data['subject'] ?? ''; 

        // Convert HTML to readable plain text format
        $content = str_replace(["<br>", "<br/>", "<br />"], "\n", $content); // Convert <br> to new lines
        $content = preg_replace('/<\/p>/i', "\n\n", $content); // Convert </p> to paragraph spacing
        $content = preg_replace('/<\/h1>/i', "\n\n", $content); // Convert </h1> to paragraph spacing
        $content = strip_tags($content); // Remove remaining HTML tags
        $content = trim(preg_replace('/\n\s*\n/', "\n\n", $content)); // Remove excessive blank lines

        // Send the email as plain text
        Mail::raw($content, function ($message) use ($data, $subject) {
            $message->to($data['to'])->subject($subject);

            if (!empty($data['attachment'])) {
                foreach ($data['attachment'] as $attachment) {
                    $message->attach($attachment);
                }
            }
        });
    }
}