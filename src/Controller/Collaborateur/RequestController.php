<?php

namespace App\Collaborateur\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RequestController extends AbstractController
{
    //NOUVELLE DEMANDE "COLLABORATEUR"
    #[Route('/new-request', name: 'app_new_request')]
    public function viewNewRequest(): Response
    {
        return $this->render('collaborateur/new_request.html.twig', [
            'page' => 'new-request',
        ]);
    }

    //HISTORIQUE DES DEMANDES "COLLABORATEUR"
    #[Route('/request-history', name: 'app_request_history')]
    public function viewRequestHistory(): Response
    {
        return $this->render('collaborateur/request_history.html.twig', [
            'page' => 'request-history',
        ]);
    }

    //DETAILS DES DEMANDES "COLLABORATEUR"
    #[Route('/detail-request-collaborateur', name: 'app_detail_request_collaborateur')]
    public function detailRequest(): Response
    {
        return $this->render('collaborateur/detail_request.html.twig', [
            'page' => 'detail-request-collaborateur',
        ]);
    }

}
