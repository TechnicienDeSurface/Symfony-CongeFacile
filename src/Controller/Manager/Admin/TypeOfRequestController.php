<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TypeOfRequestController extends AbstractController
{
    //PAGE TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-type-de-demande', name: 'app_administration_type_of_request')]
    public function viewTypeOfRequest(): Response
    {
        return $this->render('manager/admin/type-of-request/type_of_request.html.twig', [
            'page' => 'administration-type-of-request',
        ]);
    }

    //PAGE AJOUTER TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-type-de-demande', name: 'app_administration_ajouter_type_of_request')]
    public function addTypeOfRequest(): Response
    {
        return $this->render('manager/admin/type-of-request/add_type_of_request.html.twig', [
            'page' => 'administration-ajouter-type-de-demande',
        ]);
    }

    //PAGE DETAIL TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-detail-type-de-demande/{id}', name: 'app_administration_detail_type_of_request')]
    public function editTypeOfRequest(): Response
    {
        return $this->render('manager/admin/type-of-request/detail_type_of_request.html.twig', [
            'page' => 'administration-detail-type-de-demande',
        ]);
    }

    //SUPPRIMER MANAGEMENTS ET SERVICES VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-type-de-demande/{id}', name: 'app_administration_supprimer_type_of_request', methods: ['POST', 'DELETE'])]
}
