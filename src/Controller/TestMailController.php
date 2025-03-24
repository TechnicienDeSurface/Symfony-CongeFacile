<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\MailerService ;
use PDOException;

final class TestMailController extends AbstractController
{
    #[Route('/test/mail', name: 'app_test_mail')]
    public function index(MailerService $mailer): Response
    {
        $to = 'test.valdoise@gmail.com';
        $subject = 'Test Email';
        $text = 'This is a test email.';
        try {
            set_time_limit(300);  // 300 secondes = 5 minutes
            $mailer->sendEmail($to, $subject, $text);
        }catch(PDOException $e)
        {
            dd($e) ; 
        }
        return $this->render('test_mail/index.html.twig', [
            'controller_name' => 'TestMailController',
        ]);
    }
}