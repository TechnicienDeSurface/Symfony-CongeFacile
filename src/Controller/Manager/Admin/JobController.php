<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request; 
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PositionRepository as PositionRepository ; 
use App\Form\EditJobForm;
use Symfony\Component\Form\FormFactoryInterface;

class JobController extends AbstractController
{
    //PAGE JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-job', name: 'app_administration_job')]
    public function viewJob(Request $request,PositionRepository $repository): Response
    {$limit = 10;
        $currentPage = $request->query->getInt('page', 1);
        $postes = $repository->findPagination($currentPage, $limit);
        $postes = $postes->getResult() ; 
        $totalItems = $repository->countAll();
        $totalPages = ceil($totalItems / $limit);
        return $this->render('manager/admin/job/job.html.twig', ['jobs'=>$postes, 
        'currentPage' => $currentPage,
        'itemsPerPage' => $limit,
        'totalPages' => $totalPages,
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

    //PAGE DETAIL JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-detail-job/{id}', name: 'app_administration_detail_job')]
    public function editJob(Request $request, int $id, FormFactoryInterface $formFactory, PositionRepository $repository): Response
    {
        $job = $repository->find($id);

        if (!$job) {
            throw $this->createNotFoundException('Job not found');
        }

        $form = $formFactory->create(EditJobForm::class, $job);

        return $this->render('manager/admin/job/detail_job.html.twig', [
            'page' => 'administration-detail-job',
            'form' => $form->createView(),
            'jobName' => $job->getName(),
        ]);
    }
}

    //SUPPRIMER JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-job/{id}', name: 'app_administration_supprimer_job', methods: ['POST', 'DELETE'])]


