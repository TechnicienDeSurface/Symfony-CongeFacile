<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JobController extends AbstractController
{
    //PAGE JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-job', name: 'app_administration_job')]
    public function viewJob(): Response
    {
        return $this->render('manager/admin/job/job.html.twig', [
            'page' => 'administration-job',
        ]);
    }

    //PAGE AJOUTER JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-ajouter-job', name: 'app_administration_ajouter_job')]
    public function addJob(): Response
    {
        return $this->render('manager/admin/job/add_job.html.twig', [
            'page' => 'administration-ajouter-job',
        ]);
    }

    //PAGE MODIFIER JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-modifier-job', name: 'app_administration_modifier_job')]
    public function editJob(): Response
    {
        return $this->render('manager/admin/job/detail_job.html.twig', [
            'page' => 'administration-modifier-job',
        ]);
    }
}
