<?php 
// src/Service/MailerService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService {
    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function sendEmail($to, $subject, $text, $html) {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($to)
            ->subject($subject)
            ->text($text)
            ->html($html);

        $this->mailer->send($email);
    }
}