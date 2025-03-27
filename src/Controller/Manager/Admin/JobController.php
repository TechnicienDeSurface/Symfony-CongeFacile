<?php

namespace App\Controller\Manager\Admin;

use App\Form\FilterAdminDemandeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PositionRepository as PositionRepository;
use App\Entity\Position;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditJobForm;
use App\Form\AddJobForm;
use Symfony\Component\Form\FormFactoryInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class JobController extends AbstractController
{
    //PAGE JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-job/{page}', name: 'app_administration_job', methods:['GET', 'POST'])]
    public function viewJob(Request $request, PositionRepository $positionRepository, int $page = 1): Response
    {
        $form = $this->createForm(FilterAdminDemandeFormType::class);
        $form->handleRequest($request);
 
        $filters = [
            'name'         => $request->query->get('name'),
            'orderBy'    => $request->query->get('orderBy'),
        ];

        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        $order = $filters['nbperson'] ?? '';

        // Recherche dans le repository avec les filtres
        $query = $positionRepository->searchTypeOfRequest($filters, $order);
        
        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);

        try{
            $pagerfanta->setCurrentPage($page);
        }
        catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }

        return $this->render('manager/admin/job/job.html.twig', [
            'page' => 'administration-job',
            'form' => $form->createView(),
            'pager' => $pagerfanta,
            'jobs' => $pagerfanta->getCurrentPageResults(),
            'filters' => $filters,
            
        ]);
    }

    //PAGE AJOUTER JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-ajouter-job', name: 'app_administration_ajouter_job', methods:['POST'])]
    public function addJob(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de Position
        $position = new Position();

        // Créer le formulaire et le traiter
        $form = $this->createForm(AddJobForm::class, $position);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder la nouvelle position
            $entityManager->persist($position);
            $entityManager->flush();

            $this->addFlash('success', 'Le poste a été ajouté avec succès.');

            return $this->redirectToRoute('app_administration_job');
        }

        return $this->render('manager/admin/job/add_job.html.twig', [
            'form' => $form->createView(),
            'page' => 'administration-job',
        ]);

    }

    //PAGE DETAIL JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-detail-job/{id}', name: 'app_administration_detail_job', methods: ['POST'])]
    public function editJob(Request $request, Position $position, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire et le traiter
        $form = $this->createForm(EditJobForm::class, $position);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            // Si le formulaire est soumis et valide
    
            if ($form->get('edit')->isClicked()) {
                // Si le bouton "Mettre à jour" a été cliqué
                if ($form->isValid()) {
                    $entityManager->flush(); // Mettre à jour l'entité
                    $this->addFlash('success', 'Le poste a été mis à jour.');
                    return $this->redirectToRoute('app_administration_detail_job', ['id' => $position->getId()]);
                }
            } elseif ($form->get('delete')->isClicked()) {
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
