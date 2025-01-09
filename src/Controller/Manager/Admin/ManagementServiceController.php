<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManagementServiceController extends AbstractController
{
    #[Route('/administration-management-service', name: 'app_management_service')]
    public function index(): Response
    {
        return $this->render('management_service/index.html.twig', [
            'controller_name' => 'ManagementServiceController',
        ]);
    }
}
