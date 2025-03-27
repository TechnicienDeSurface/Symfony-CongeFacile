<?php 
// scheduler.php
require_once __DIR__ . '/vendor/autoload.php';

use GO\Scheduler;
use App\Entity\Person ;
use App\Entity\User ;
use App\Entity\Request ;
use App\Repository\UserRepository ; 
use App\Repository\PersonRepository ;
use App\Repository\RequestRepository ;
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
// Récupérer le conteneur de services
// Récupérer le conteneur de services
$container = $kernel->getContainer();

// Récupérer l'EntityManager
$entityManager = $container->get('doctrine.orm.entity_manager');

// Récupérer les repositories des entités
$userRepository = $entityManager->getRepository(User::class);
$personRepository = $entityManager->getRepository(Person::class);
$requestRepository = $entityManager->getRepository(Request::class);

// Récupérer toutes les données des entités
$users = $userRepository->findBy([],[]);
$persons = $personRepository->findBy([],[]);
$requests = $requestRepository->findBy([],[]);

// Planifier l'envoi d'email toutes les minutes
$scheduler->call(function() use ($mailerService, $persons, $requests, $users) {
    foreach ($persons as $row) {
        foreach($requests as $request){
            if($request->collaborator->id == $row->id){
                $ask = $request ; 
            }
        }
        foreach($users as $user){
            if($user->person->id == $row->id){
                $userPerson = $user ; 
                $to = $userPerson->email ; 
            }
        }
        
        // Logique pour chaque personne
        if(isset($to)){
            if($row->alert_before_vacations == true){
                $startAt = $request->getStartAt() ; 
                $now = new \DateTime() ;
                $interval = $now->diff($startAt); //Calculer la différence entre 2 dates
                if ($interval->days == 7 && $interval->invert == 0) {
                    $mailerService->sendEmail($to, 'Rappel de congé', 'Vous prenez vos congé dans 1 semaine. '); 
                }   
            }
            if($row->alert_on_answer == true){
                if($request->answer === true){
                    $mailerService->sendEmail($to, 'Demande de congé - Réponse', 'Votre manager a répondu à votre demande de congé.');
                } 
            }
        }

    }
})->everyMinute();

// Exécuter le scheduler
$scheduler->run();
