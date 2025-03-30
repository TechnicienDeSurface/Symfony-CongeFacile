<?php 
// src/Service/MailerService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\Entity\User ; 
use App\Repository\UserRepository ; 
use App\Repository\RequestRepository ; 
use Symfony\Component\Mime\Email;

class MailerService {
    private $mailer;
    private $eventDispatcher;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function sendEmail($to, $subject, $text) {
        $email = (new Email())
            ->from('heddy.mameri@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($text); 
        $this->mailer->send($email);
    }

    // public function checkLeaves(UserRepository $repository, RequestRepository $request_repository) {
    //     $oneWeekLater = (new \DateTime())->modify('+1 week');
    //     $users = $repository->All() ; 
    //     foreach ($users as $row) {
    //         $person = $row->getPerson();
    //         $request = $request_repository->find($person->getId()) ; 
    //         if ($request->getStartDate() == $oneWeekLater && $person->getAlertOnBeforeVacation()) {
    //             $this->eventDispatcher->dispatch(new LeaveReminderEvent($row), LeaveReminderEvent::NAME);
    //         }
    //     }
    // }
}