<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Services\RabbitMQService;
use App\Services\EngineService;
use App\Services\EmailLogService;
use PhpAmqpLib\Message\AMQPMessage;

class EmailEngine extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume emails from RabbitMQ queues and process them';

    private $RabbitMQService;
    private $EngineService;
    private $EmailLogService;

    public function __construct(RabbitMQService $RabbitMQService, EngineService $EngineService, EmailLogService $EmailLogService)
    {
        parent::__construct();
        $this->RabbitMQService = $RabbitMQService;
        $this->EngineService = $EngineService;
        $this->EmailLogService = $EmailLogService;
    }

    public function handle()
    {
        // Start consuming messages from the RabbitMQ queue
        $this->RabbitMQService->consume('email_queue', [$this, 'processEmail']);
        $this->RabbitMQService->wait();
    }

    public function processEmail(AMQPMessage $msg)
    {
        $data = json_decode($msg->getBody(), true);
    
        // Validate the message format
        if (!is_array($data) || empty($data['secret'])) {
            $this->error("Invalid message format: Missing 'secret' key.");
            return;
        }
    
        $application = Application::where('secret_key', $data['secret'])->first();
        if (!$application) {
            // Log failure if application is not found
            $this->EmailLogService->updateLog($data, 'failed', 'Invalid secret key', $application->id);
            return;
        }
    
        try {
            // Attempt to send the email using the EngineService
            $this->EngineService->sendEmail($data);
    
            // Log the email as successful
            $this->EmailLogService->updateLog($data, 'success', null, $application->id);
            $this->info("Email sent successfully to: " . $data['to']);
        } catch (\Exception $e) {
            // Log the email as failed with the error message
            $this->EmailLogService->updateLog($data, 'failed', $e->getMessage(), $application->id);
            $this->error("Failed to send email to: " . $data['to'] . " - " . $e->getMessage());
        }
    }    
}