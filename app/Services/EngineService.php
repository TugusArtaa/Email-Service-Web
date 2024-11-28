<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EngineService
{
    // Method untuk mengirim email yang dikonsumsi dari RabbitMQ
    public function sendEmail(array $data)
    {
        // Define your standard header and footer (for HTML emails)
        $header = "--------------------------------\n" .
            "[Your Company Logo]\n" .
            "Hello, this is an automated message from [Your Company].\n" .
            "--------------------------------\n\n";

        $footer = "\n\n--------------------------------\n" .
            "Thank you for using our services!\n" .
            "Contact us: support@yourcompany.com\n" .
            "Unsubscribe here: [link]\n" .
            "--------------------------------";

        // Get content and subject from data and handle null or empty cases
        $content = isset($data['content']) ? $data['content'] : '';
        $subject = isset($data['subject']) ? $data['subject'] : ''; // Default value for subject

        // Check if content is HTML or plain text
        if (preg_match("/<[^<]+>/", $content)) {
            // If the content is HTML, add header and footer and use Mail::html
            $content = $header . $content . $footer;
            Mail::html($content, function ($message) use ($data, $subject) {
                $message->to($data['to'])
                    ->subject($subject); // Ensure subject is a string
                if (!empty($data['attachment'])) {
                    foreach ($data['attachment'] as $attachment) {
                        $message->attach($attachment);
                    }
                }
            });
        } else {
            // If the content is not HTML, use Mail::raw for plain text
            $content = $header . strip_tags($content) . $footer; // Remove HTML tags for plain text
            Mail::raw($content, function ($message) use ($data, $subject) {
                $message->to($data['to'])
                    ->subject($subject); // Ensure subject is a string
                if (!empty($data['attachment'])) {
                    foreach ($data['attachment'] as $attachment) {
                        $message->attach($attachment);
                    }
                }
            });
        }
    }
}