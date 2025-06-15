<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EngineService
{
    // Function untuk mengonsumsi pesan email dari RabbitMQ dan mengirim email
    public function sendEmail(array $data)
    {
        // Validasi data wajib
        if (empty($data) || !is_array($data)) {
            throw new \InvalidArgumentException("Data email tidak valid (null atau bukan array).");
        }
        if (!isset($data['to']) || empty($data['to'])) {
            throw new \InvalidArgumentException("Field 'to' harus diisi.");
        }
        if (!isset($data['secret']) || empty($data['secret'])) {
            throw new \InvalidArgumentException("Field 'secret' harus diisi.");
        }
        if (!isset($data['priority']) || empty($data['priority'])) {
            throw new \InvalidArgumentException("Field 'priority' harus diisi.");
        }

        $subject = isset($data['subject']) ? $data['subject'] : '';
        $rawContent = isset($data['content']) ? $data['content'] : '';
        $content = $this->convertHtmlToPlainText($rawContent);

        // Download attachments terlebih dahulu
        $tempFiles = [];
        if (!empty($data['attachment']) && is_array($data['attachment'])) {
            foreach ($data['attachment'] as $attachmentUrl) {
                // Skip jika kosong
                if ($attachmentUrl === null || $attachmentUrl === '') {
                    continue;
                }
                // Validasi URL
                if (!filter_var($attachmentUrl, FILTER_VALIDATE_URL)) {
                    foreach ($tempFiles as $file) {
                        @unlink($file['path']);
                    }
                    throw new \Exception("Attachment URL tidak valid: $attachmentUrl");
                }
                $tempPath = tempnam(sys_get_temp_dir(), 'mail_attach_');
                try {
                    $fileContents = @file_get_contents($attachmentUrl);
                    if ($fileContents === false) {
                        foreach ($tempFiles as $file) {
                            @unlink($file['path']);
                        }
                        throw new \Exception("Gagal mengunduh attachment: $attachmentUrl");
                    }
                    file_put_contents($tempPath, $fileContents);
                    $tempFiles[] = [
                        'path' => $tempPath,
                        'name' => basename(parse_url($attachmentUrl, PHP_URL_PATH))
                    ];
                } catch (\Exception $e) {
                    foreach ($tempFiles as $file) {
                        @unlink($file['path']);
                    }
                    throw new \Exception($e->getMessage());
                }
            }
        }

        // Kirim email sebagai teks biasa
        Mail::raw($content, function ($message) use ($data, $subject, $tempFiles) {
            $message->to($data['to'])->subject($subject);

            foreach ($tempFiles as $file) {
                $message->attach($file['path'], ['as' => $file['name']]);
            }
        });

        // Hapus file sementara setelah email terkirim
        foreach ($tempFiles as $file) {
            @unlink($file['path']);
        }
    }

    // Function untuk mengonversi HTML ke teks biasa
    private function convertHtmlToPlainText(string $html): string
    {
        $text = str_replace(["<br>", "<br/>", "<br />"], "\n", $html); // Convert <br> menjadi barisan baru
        $text = preg_replace('/<\/?(h[1-6]|p)>/i', "\n\n", $text); // Convert <h1> hingga <h6> dan <p> menjadi barisan baru
        $text = strip_tags($text); // Hapus tag HTML lainnya
        return trim(preg_replace('/\n\s*\n/', "\n\n", $text)); // Hapus barisan kosong berlebihan
    }
}