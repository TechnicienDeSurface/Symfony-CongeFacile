<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManagementServiceController extends AbstractController
{
    //PAGE MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[Route('/administration-management-service', name: 'app_administration_management_service')]
    public function viewManagementService(): Response
    {
        return $this->render('manager/admin/management-service/management_service.html.twig', [
            'page' => 'administration-management-service',
        ]);
    }

    //PAGE AJOUTER MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-management-service', name: 'app_administration_ajouter_management_service')]
    public function addManagementService(): Response
    {
        return $this->render('manager/admin/management-service/add_management_service.html.twig', [
            'page' => 'administration-ajouter-management-service',
        ]);
    }

    //PAGE DETAIL MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[Route('/administration-detail-management-service/{id}', name: 'app_administration_detail_management_service')]
    public function editManagementService(): Response
    {
        return $this->render('manager/admin/management-service/detail_management_service.html.twig', [
            'page' => 'administration-detail-management-service',
        ]);
    }

    //SUPPRIMER MANAGEMENTS ET SERVICES VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-management-service/{id}', name: 'app_administration_supprimer_management-service', methods: ['POST', 'DELETE'])]
}
