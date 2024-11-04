<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Services\RabbitMQService;
use App\Services\EmailService;
use App\Services\EmailLogService;
use PhpAmqpLib\Message\AMQPMessage;
use Exception;

class EmailEngine extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Send emails from RabbitMQ queues and log the result';

    private $rabbitMQService;
    private $emailService;
    private $emailLogService;

    public function __construct(RabbitMQService $rabbitMQService, EmailService $emailService, EmailLogService $emailLogService)
    {
        parent::__construct();

        $this->rabbitMQService = $rabbitMQService;
        $this->emailService = $emailService;
        $this->emailLogService = $emailLogService;
    }

    public function handle()
    {
        $this->rabbitMQService->consume('email_high_priority', [$this, 'processEmail']);
        $this->rabbitMQService->consume('email_medium_priority', [$this, 'processEmail']);
        $this->rabbitMQService->consume('email_low_priority', [$this, 'processEmail']);

        $this->rabbitMQService->wait();
    }

    public function processEmail(AMQPMessage $msg)
    {
        $data = json_decode($msg->getBody(), true);

        $application = Application::where('secret_key', $data['secret'])->first();
        if (!$application) {
            $this->emailLogService->logEmail($data, 'failed', 'Invalid secret key');
            return;
        }

        try {
            $this->emailService->sendEmail($data);
            $this->emailLogService->logEmail($data, 'success', null, $application->id);
            $this->info("Email sent to: " . $data['to']);
        } catch (Exception $e) {
            $this->emailLogService->logEmail($data, 'failed', $e->getMessage(), $application->id);
            $this->error("Failed to send email to: " . $data['to'] . " - " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->rabbitMQService->close();
    }
}
