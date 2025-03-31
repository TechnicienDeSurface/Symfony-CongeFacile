<?php

namespace App\Command;

use App\Entity\Person ;
use App\Entity\User ;
use App\Entity\Request ;
use App\Service\MailerService;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'MailCommand',
    description: 'Add a short description for your command',
)]
class MailCommand extends Command
{
    protected static $defaultName = 'app:send-email';
    private $mailer;
    private EntityManagerInterface $entityManager;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));

            // Configurer le transport de mail
$transport = Transport::fromDsn($_ENV['MAILER_DSN']);
$mailer = new Mailer($transport);
$mailerService = new MailerService($mailer);

// Récupérer les repositories des entités
$userRepository = $this->entityManager->getRepository(User::class);
$personRepository = $this->entityManager->getRepository(Person::class);
$requestRepository = $this->entityManager->getRepository(Request::class);

// Récupérer toutes les données des entités
$users = $userRepository->findBy([],[]);
$persons = $personRepository->findBy([],[]);
$requests = $requestRepository->findBy([],[]);

// Planifier l'envoi d'email toutes les minutes

    foreach ($persons as $row) {
        foreach($requests as $request){
            if($request->collaborator->id == $row->id){
                $ask = $request; 
            }
        }
        foreach($users as $user){
            if($user->person->id == $row->id){
                $userPerson = $user; 
                $to = $userPerson->email; 
            }
        }
        
        // Logique pour chaque personne
        if(isset($to)){
            if($row->alert_before_vacations == true){
                $startAt = $request->getStartAt(); 
                $now = new \DateTime();
                $interval = $now->diff($startAt); //Calculer la différence entre 2 dates
                if ($interval->days == 7 && $interval->invert == 0) {
                    $mailerService->sendEmail($to, 'Rappel de congé', 'Vous prenez vos congé dans 1 semaine. '); 
                }   
            }
            if($row->alert_on_answer == true){
                if($ask->answer === true){
                    $mailerService->sendEmail($to, 'Demande de congé - Réponse', 'Votre manager a répondu à votre demande de congé.');
                } 
            }
        }

    }
            $output->writeln('Email envoyé avec succès!');

        return Command::SUCCESS;
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
