<?php

namespace App\Controller\Collaborateur;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InformationController extends AbstractController
{
    //PAGE INFORMATIONS "COLLABORATEUR"
    #[Route('/information-collaborateur', name: 'app_information_collaborateur')]
    public function index(): Response
    {
        return $this->render('collaborateur/information.html.twig', [
            'page' => 'information-collaborateur',
        ]);
    }
}
