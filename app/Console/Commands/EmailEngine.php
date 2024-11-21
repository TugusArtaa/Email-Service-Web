<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Models\EmailLog;
use App\Services\RabbitMQService;
use App\Services\EngineService;
use PhpAmqpLib\Message\AMQPMessage;
use Exception;

class EmailEngine extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Send emails from RabbitMQ queues and log the result';

    private $RabbitMQService;
    private $EngineService;


    public function __construct(RabbitMQService $RabbitMQService, EngineService $EngineService)
    {
        parent::__construct();

        $this->RabbitMQService = $RabbitMQService;
        $this->EngineService = $EngineService;
    }

    public function handle()
    {
        $this->RabbitMQService->consume('email_queue', [$this, 'processEmail']);

        $this->RabbitMQService->wait();
    }

    public function processEmail(AMQPMessage $msg)
    {
        $data = json_decode($msg->getBody(), true);
    
        // Check if the message contains an email_log_id
        if (isset($data['id']) && $data['id']) {
            // Look for the log entry in the database
            $emailLog = EmailLog::find($data['id']);
            
            if ($emailLog) {
                // If the log exists, update the status and resend the email
                try {
                    $this->EngineService->sendEmail($data); // Send email
                    $this->EngineService->updateLog($data, 'success', null, $emailLog->application_id); // Update log
                    $this->info("Email log updated and email sent to: " . $data['to']);
                } catch (Exception $e) {
                    $this->EngineService->updateLog($data, 'failed', $e->getMessage(), $emailLog->application_id); // Log failure
                    $this->error("Failed to send email to: " . $data['to'] . " - " . $e->getMessage());
                }
            } else {
                // If the log does not exist, handle the case appropriately
                $this->error("Email log ID not found: " . $data['email_log_id']);
            }
    
            return;
        }
    
        // If no email_log_id is provided, process the email as a new send
        $application = Application::where('secret_key', $data['secret'])->first();
        if (!$application) {
            $this->EngineService->logEmail($data, 'failed', 'Invalid secret key');
            return;
        }
    
        try {
            // Send the email as a new email
            $this->EngineService->sendEmail($data);
    
            // Log the successful email
            $this->EngineService->logEmail($data, 'success', null, $application->id);
            $this->info("Email sent to: " . $data['to']);
        } catch (Exception $e) {
            // Log failure
            $this->EngineService->logEmail($data, 'failed', $e->getMessage(), $application->id);
            $this->error("Failed to send email to: " . $data['to'] . " - " . $e->getMessage());
        }
    }    

    public function __destruct()
    {
        $this->RabbitMQService->close();
    }
}
