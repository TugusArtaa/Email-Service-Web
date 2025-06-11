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
    // Mendefinisikan signature command yang akan digunakan di terminal
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Mengonsumsi Email dari RabbitMQ dan memroses untuk dikirim';

    private $RabbitMQService;
    private $EngineService;
    private $EmailLogService;

    // Function constructor untuk menginisialisasi service yang diperlukan
    public function __construct(RabbitMQService $RabbitMQService, EngineService $EngineService, EmailLogService $EmailLogService)
    {
        parent::__construct();
        $this->RabbitMQService = $RabbitMQService;
        $this->EngineService = $EngineService;
        $this->EmailLogService = $EmailLogService;
    }

    // Function untuk memulai konsumsi pesan dari RabbitMQ
    public function handle()
    {
        $this->info('Email Engine dimulai...');
        $this->RabbitMQService->consume('email_queue', [$this, 'processEmail']);
        $this->RabbitMQService->wait();
    }

    // Function untuk memvalidasi aplikasi berdasarkan secret key
    private function validateApplication($secret)
    {
        if (empty($secret)) {
            return [null, 'Format pesan invalid: Secret key tidak ada.'];
        }
        $application = Application::where('secret_key', $secret)->first();
        if (!$application) {
            return [null, 'Secret key invalid'];
        }
        return [$application, null];
    }

    // Function untuk memproses pesan email yang diterima dari RabbitMQ
    public function processEmail(AMQPMessage $msg)
    {
        // Untuk memastikan bahwa pesan yang diterima adalah dalam format JSON valid
        try {
            $data = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->error("Malformed JSON: " . $e->getMessage());
            return;
        }

        [$application, $error] = $this->validateApplication($data['secret'] ?? null);
        if ($error) {
            $this->error($error);
            return;
        }

        // Untuk logika pengiriman email
        try {
            $this->EngineService->sendEmail($data);
            $this->EmailLogService->updateLog($data, 'success', null, $application->id);
            $this->info("Email sukses terkirim kepada: " . $data['to']);
        } catch (\Exception $e) {
            $this->EmailLogService->updateLog($data, 'failed', $e->getMessage(), $application->id);
            $this->error("Gagal mengirim email kepada: " . $data['to'] . " - " . $e->getMessage());
        }
    }
}