<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManagerController extends AbstractController
{
    //PAGE MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-manager', name: 'app_administration_manager')]
    public function viewManager(): Response
    {
        return $this->render('manager/admin/manager/manager.html.twig', [
            'page' => 'administration-manager',
        ]);
    }

    //PAGE AJOUTER MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-manager', name: 'app_administration_ajouter_manager')]
    public function addManager(): Response
    {
        return $this->render('manager/admin/manager/add_manager.html.twig', [
            'page' => 'administration-ajouter-manager',
        ]);
    }

    //PAGE DETAIL MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-detail-manager/{id}', name: 'app_administration_detail_manager')]
    public function editManager(): Response
    {
        return $this->render('manager/admin/manager/detail_manager.html.twig', [
            'page' => 'administration-detail-manager',
        ]);
    }

    //SUPPRIMER MANAGER VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-manager/{id}', name: 'app_administration_supprimer_manager', methods: ['POST', 'DELETE'])]
}
