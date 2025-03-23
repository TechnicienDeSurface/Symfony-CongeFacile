<?php 
// src/EventListener/LeaveReminderListener.php
namespace App\EventListener;

use App\Service\MailerService;
use App\Entity\User ; 
use App\Event\LeaveReminderEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EventListener {
    private $mailerService;

    public function __construct(MailerService $mailerService) {
        $this->mailerService = $mailerService;
    }

    public function onLeaveReminder(LeaveReminderEvent $event) {
        $leave = $event->getLeave();
        $email = $leave->getUser()->getEmail();
        $subject = 'Rappel de congé';
        $text = 'Votre congé commence dans une semaine.';
        $html = '<p>Votre congé commence dans une semaine.</p>';

        $this->mailerService->sendEmail($email, $subject, $text, $html);
    }
}