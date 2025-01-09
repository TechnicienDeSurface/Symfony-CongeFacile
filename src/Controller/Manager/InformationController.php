<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InformationController extends AbstractController
{
    //PAGE D'INFORMATIONS SUR LES DONNEES DU MANAGER
    #[Route('/information-manager', name: 'app_information_manager')]
    public function viewInformationManager(): Response
    {
        return $this->render('manager/information.html.twig', [
            'page' => 'information-manager',
        ]);
    }
}
