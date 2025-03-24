<?php 
// scheduler.php
require_once __DIR__ . '/vendor/autoload.php';

use GO\Scheduler;
use App\Service\MailerService;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Configurer le transport de mail
$transport = Transport::fromDsn($_ENV['MAILER_DSN']);
$mailer = new Mailer($transport);
$mailerService = new MailerService($mailer);

// Créer un nouveau scheduler
$scheduler = new Scheduler();

// Planifier l'envoi d'email toutes les minutes
$scheduler->call(function() use ($mailerService) {
    $mailerService->sendEmail('recipient@example.com', 'Scheduled Email', 'This is a scheduled email sent every minute.');
})->everyMinute();

// Exécuter le scheduler
$scheduler->run();
