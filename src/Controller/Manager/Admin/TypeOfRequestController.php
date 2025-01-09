<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TypeOfRequestController extends AbstractController
{
    #[Route('/type/of/request', name: 'app_type_of_request')]
    public function index(): Response
    {
        return $this->render('type_of_request/index.html.twig', [
            'controller_name' => 'TypeOfRequestController',
        ]);
    }
}
