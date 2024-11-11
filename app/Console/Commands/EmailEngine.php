<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
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

        $application = Application::where('secret_key', $data['secret'])->first();
        if (!$application) {
            $this->EngineService->logEmail($data, 'failed', 'Invalid secret key');
            return;
        }

        try {
            $this->EngineService->sendEmail($data);
            $this->EngineService->logEmail($data, 'success', null, $application->id);
            $this->info("Email sent to: " . $data['to']);
        } catch (Exception $e) {
            $this->EngineService->logEmail($data, 'failed', $e->getMessage(), $application->id);
            $this->error("Failed to send email to: " . $data['to'] . " - " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->RabbitMQService->close();
    }
}
