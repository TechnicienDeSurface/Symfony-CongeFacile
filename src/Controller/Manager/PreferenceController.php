<?php

namespace App\Manager\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreferenceController extends AbstractController
{
    #[Route('/preference', name: 'app_preference')]
    public function index(): Response
    {
        return $this->render('preference/index.html.twig', [
            'controller_name' => 'PreferenceController',
        ]);
    }
}
