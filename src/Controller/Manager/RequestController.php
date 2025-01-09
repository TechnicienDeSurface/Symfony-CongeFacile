<?php

namespace App\Manager\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RequestController extends AbstractController
{
    #[Route('/request/pending', name: 'app_request_pending')]
    public function index(): Response
    {
        return $this->render('request_pending/index.html.twig', [
            'controller_name' => 'RequestPendingController',
        ]);
    }
}
