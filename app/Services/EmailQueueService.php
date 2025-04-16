<?php

namespace App\Services;

use App\Http\Requests\SendEmailRequest;
use App\Http\Requests\ExtractEmailRequest;
use App\Models\Application;
use PhpAmqpLib\Message\AMQPMessage;
use App\Helpers\ResponseHelper; 
use App\Models\EmailLog;
use Maatwebsite\Excel\Facades\Excel;

class EmailQueueService
{
    private $channel;

    public function __construct(RabbitMQService $RabbitMQService)
    {
        $this->channel = $RabbitMQService->connect();
    }

    //Method untuk memproses dan memasukan email ke dalam queue
    public function processAndQueueEmails(array $emails, string $secret)
    {
        $application = Application::where('secret_key', $secret)
        ->where('status', 'enabled')
        ->first();

        if (!$application) {
            return ['error' => 'Secret key tidak valid'];
        }

        $messages = [];
        foreach ($emails as $mail) {
            try {
                // Map priority strings to numerical values
                $priorityMap = [
                    'low' => 1,
                    'medium' => 2,
                    'high' => 3,
                ];

                // Convert the priority string to a numerical value
                $priority = $priorityMap[$mail['priority']] ?? 2; // Default to 'medium' if not provided or invalid

                // Ensure subject and content are not null but empty if not provided
                $subject = isset($mail['subject']) ? $mail['subject'] : '';
                $content = isset($mail['content']) ? $mail['content'] : '';

                // Prepare the data with the numeric priority for storing in the DB and RabbitMQ
                // Map priority strings to numerical values
                $priorityMap = [
                    'low' => 1,
                    'medium' => 2,
                    'high' => 3,
                ];

                // Convert the priority string to a numerical value
                $priority = $priorityMap[$mail['priority']] ?? 2; // Default to 'medium' if not provided or invalid

                // Ensure subject and content are not null but empty if not provided
                $subject = isset($mail['subject']) ? $mail['subject'] : '';
                $content = isset($mail['content']) ? $mail['content'] : '';

                // Prepare the data with the numeric priority for storing in the DB and RabbitMQ
                $messageData = [
                    'id' => $mail['id'],
                    'to' => $mail['to'],
                    'subject' => $subject,
                    'content' => $content,
                    'attachment' => $mail['attachment'] ?? [],
                    'priority' => $mail['priority'], 
                    'secret' => $secret,
                ];

                // Create a message with the RabbitMQ-compatible priority value
                $msg = new AMQPMessage(
                    json_encode($messageData),
                    [
                        'delivery_mode' => 2, // Make message persistent
                        'priority' => $priority, // Use the numeric priority for RabbitMQ
                    ]
                );

                // Publish the message to RabbitMQ
                $this->channel->basic_publish($msg, 'email_exchange', 'email');

                $messageDataReturn = $messageData;
                unset($messageDataReturn['id']);
                unset($messageDataReturn['secret']);

                $messages[] = $messageDataReturn; // Store the processed message data for response
            } catch (\Exception $e) {
                return ['error' => 'Antrian error: ' . $e->getMessage()];
            }
        }

        return ['messages' => $messages];
    }

    //Method untuk memproses email dari file excel
    public function processEmailsFromExcel($file)
    {
        $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);
    
        if (empty($data) || !isset($data[0])) {
            return ['error' => 'File Excel tidak valid atau kosong'];
        }
    
        $rows = $data[0]; // First sheet
        $messages = [];
        $validationErrors = [];
    
        // First pass: Validate all rows
        foreach ($rows as $index => $row) {
            // Skip the header row if present
            if ($index === 0 && strcasecmp($row[0] ?? '', 'secret') === 0) {
                continue;
            }
    
            // Skip entirely empty rows
            if (empty(array_filter($row, fn($value) => trim($value) !== ''))) {
                continue;
            }//fixed
    
            $processedRow = [
                'secret' => trim($row[0] ?? null),
                'to' => trim($row[1] ?? null),
                'content' => $row[2] ?? '', // Default to empty string
                'subject' => $row[3] ?? '', // Default to empty string
                'priority' => strtolower(trim($row[4] ?? '')), // Ensure lowercase, trim whitespace
                'attachment' => isset($row[5]) ? explode(',', $row[5]) : [], // Default to empty array
            ];
    
            // Validate the row
            $errors = [];
            if (empty($processedRow['secret'])) {
                $errors[] = 'Secret key wajib diisi';
            }
            if (empty($processedRow['to'])) {
                $errors[] = 'Email penerima ("to") wajib diisi';
            } elseif (!filter_var($processedRow['to'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid untuk kolom "to"';
            }
            if (empty($processedRow['priority']) || !in_array($processedRow['priority'], ['low', 'medium', 'high'], true)) {
                $errors[] = 'Prioritas wajib diisi dan harus salah satu dari: low, medium, high';
            }
    
            // Check if the application exists
            if (!empty($processedRow['secret'])) {
                $application = Application::where('secret_key', $processedRow['secret'])->first();

                if (!$application) {
                    $errors[] = 'Secret key tidak valid';
                } elseif ($application->status !== 'enabled') {
                    $errors[] = 'Status aplikasi disabled';
                } else {
                    $processedRow['application_id'] = $application->id;
                }                
            }
    
            if ($errors) {
                $validationErrors[] = [
                    'row' => $index + 1, // Excel rows are 1-based
                    'errors' => $errors,
                ];
                continue;
            }
    
            // Add valid data for processing
            $messages[] = $processedRow;
        }
    
        // If there are validation errors, return them and stop processing
        if ($validationErrors) {
            return ['validationErrors' => $validationErrors];
        }
    
        // If no valid messages exist after validation, return an error
        if (empty($messages)) {
            return ['error' => 'Tidak ada data email yang valid ditemukan dalam file Excel'];
        }
    
        // Sort by priority
        usort($messages, function ($a, $b) {
            $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
            return $priorityMap[$b['priority']] <=> $priorityMap[$a['priority']];
        });
    
        // Queue emails
        foreach ($messages as &$message) {
            // Create email log
            $emailLog = app(EmailLogService::class)->logEmail($message, $message['application_id']);
            $message['id'] = $emailLog->id;
        }
    
        $result = $this->processAndQueueEmails($messages, $messages[0]['secret']);
    
        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }
    
        return ['messages' => $result['messages']];
    }

    // //Method untuk mengekstrak data email log berdasarkan ID
    public function extractEmailLogData(ExtractEmailRequest $request)
    {
        $id = $request->input('id');
        $emailLog = EmailLog::find($id);

        if (!$emailLog) {
            return errorResponse('Data log email tidak ditemukan', 404);
        }

        try {
            $emailData = json_decode($emailLog->request, true);


            if (json_last_error() !== JSON_ERROR_NONE) {
                return validationError(['Format JSON pada data log email tidak valid.'], 422);
            }

            return responseWithData('Data log email berhasil di ekstrak', [
                'to' => $emailData['to'] ?? '',
                'subject' => $emailData['subject'] ?? '',
                'content' => $emailData['content'] ?? '',
                'priority' => $emailData['priority'] ?? '',
                'attachment' => $emailData['attachment'] ?? [],
                'status' => $emailLog['status'] ?? '',
                'error_message' => $emailLog['error_message'] ?? '',
                'secret' => $emailData['secret'] ?? '',
            ]);
        } catch (\Exception $e) {
            return queueError('Terjadi kesalahan saat memproses data log email.');
        }
    }


    public function retryEmails(array $data)
    {
        $emailLog = EmailLog::find($data['id']);
    
        if (!$emailLog) {
            return ['error' => 'Email log not found'];
        }
    
        $emailData = json_decode($emailLog->request, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid email log data'];
        }
    
        // Update email data with new inputs from retry request
        $emailData['id'] = $data['id']; 
        $emailData['to'] = $data['mail'][0]['to'] ?? $emailData['to'];
        $emailData['subject'] = $data['mail'][0]['subject'] ?? $emailData['subject'];
        $emailData['content'] = $data['mail'][0]['content'] ?? $emailData['content'];
        $emailData['attachment'] = $data['mail'][0]['attachment'] ?? $emailData['attachment'];

        // Log the new retried email
        $applicationId = $emailLog->application_id;
        $newEmailLog = app(EmailLogService::class)->logEmail($emailData, $applicationId);
        $emailData['id'] = $newEmailLog->id; // Assign the new log ID to the email data

        try {
            $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
            $priority = $priorityMap[$emailData['priority']] ?? 2;
    
            $msg = new AMQPMessage(
                json_encode($emailData),
                [
                    'delivery_mode' => 2,
                    'priority' => $priority,
                ]
            );
    
            $this->channel->basic_publish($msg, 'email_exchange', 'email');
    
            return ['message' => $emailData];
        } catch (\Exception $e) {
            return ['error' => 'Kirim ulang gagal:' . $e->getMessage()];
        }
    }

}