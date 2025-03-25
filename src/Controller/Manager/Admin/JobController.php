<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PositionRepository as PositionRepository;
use App\Entity\Position;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditJobForm;
use Symfony\Component\Form\FormFactoryInterface;

class JobController extends AbstractController
{
    //PAGE JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-job', name: 'app_administration_job')]
    public function viewJob(Request $request, PositionRepository $repository): Response
    {
        $limit = 10;
        $currentPage = $request->query->getInt('page', 1);
        $postes = $repository->findPagination($currentPage, $limit);
        $postes = $postes->getResult();
        $totalItems = $repository->countAll();
        $totalPages = ceil($totalItems / $limit);

        return $this->render('manager/admin/job/job.html.twig', [
            'jobs' => $postes,
            'currentPage' => $currentPage,
            'itemsPerPage' => $limit,
            'totalPages' => $totalPages,
            'page' => 'administration-job'
        ]);
    }

    //PAGE AJOUTER JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-ajouter-job', name: 'app_administration_ajouter_job')]
    public function addJob(): Response
    {
        return $this->render('manager/admin/job/add_job.html.twig', [
            'page' => 'administration-job',
        ]);
    }

    //PAGE DETAIL JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-detail-job/{id}', name: 'app_administration_detail_job')]
    public function editJob(Request $request, Position $position, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire et le traiter
        $form = $this->createForm(EditJobForm::class, $position);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            // Si le formulaire est soumis et valide
    
            if ($form->get('edit')->isSubmitted()) {
                // Si le bouton "Mettre à jour" a été cliqué
                if ($form->isValid()) {
                    $entityManager->flush(); // Mettre à jour l'entité
                    $this->addFlash('success', 'Le poste a été mis à jour.');
                    return $this->redirectToRoute('app_administration_detail_job', ['id' => $position->getId()]);
                }
            } elseif ($form->get('delete')->isSubmitted()) {
                // Si le bouton "Supprimer" a été cliqué
                // Vérifier si la position existe encore
                if ($position) {
                    $entityManager->remove($position); // Supprimer l'entité
                    $entityManager->flush(); // Appliquer la suppression en base
                    $this->addFlash('warning', 'Le poste a été supprimé.');
                    return $this->redirectToRoute('app_administration_job'); // Redirection après suppression
                } else {
                    $this->addFlash('error', 'Le poste n\'existe pas.');
                }
            }
        }
    
        return $this->render('manager/admin/job/detail_job.html.twig', [
            'page' => 'administration-job',
            'form' => $form->createView(),
        ]);
    }
    
}

    //SUPPRIMER JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-job/{id}', name: 'app_administration_supprimer_job', methods: ['POST', 'DELETE'])]
