<?php

namespace App\Controller\Manager\Admin;

use App\Form\FilterAdminDemandeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PositionRepository as PositionRepository;
use App\Repository\PersonRepository;
use App\Entity\Position;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditJobForm;
use App\Form\AddJobForm;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class JobController extends AbstractController
{
    //PAGE JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[IsGranted('ROLE_MANAGER')]
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

        $order = $filters['totalnbdemande'] ?? '';

        // Recherche dans le repository avec les filtres
        $query = $positionRepository->searchTypeOfRequest($filters, $order);
        
        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);

        try{
            if($form->isSubmitted()) {
                $page = 1;
            }
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
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-ajouter-job', name: 'app_administration_ajouter_job', methods:['GET','POST'])]
    public function addJob(PositionRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de Position
        $position = new Position();
        $positions = $repository->findBy([],[]);
        $verif = false; 
        // Créer le formulaire et le traiter
        $form = $this->createForm(EditJobForm::class, $position, [
            'submit_label' => 'Ajouter',
         ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder la nouvelle position
            foreach($positions as $row){
                if($position->getName() == $row->getName()){
                    $verif = true; 
                };
            }
            if($verif === true){
                $this->addFlash('error','Erreur ce poste existe déjà');
            }else{
                $entityManager->persist($position);
                $entityManager->flush();
                $this->addFlash('success','Le poste a été ajouté avec succès.');
                return $this->redirectToRoute('app_administration_job');
            }
            
        }

        return $this->render('manager/admin/job/add_job.html.twig', [
            'form' => $form->createView(),
            'page' => 'administration-job',
        ]);

    }

    //PAGE DETAIL JOB VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-detail-job/{id}', name: 'app_administration_detail_job', methods: ['GET','POST'])]
    public function editJob(PersonRepository $repository, Request $request, Position $position, EntityManagerInterface $entityManager, int $id): Response
    {
        $form = $this->createForm(EditJobForm::class, $position, [
           'submit_label' => 'Mettre à jour',
        ]);
        $errorLinks = $repository->findPersonByPosition($position->getId());
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            if($form->get('edit')->isClicked()) {
                if($form->isValid()) {
                    $entityManager->flush();
                    $this->addFlash('success', 'Le poste a été mis à jour avec succès.');
                    return $this->redirectToRoute('app_administration_job');
                }else{
                    $this->addFlash('error', 'Erreurs de validations.');
                    return $this->redirectToRoute('app_administration_detail_job', ['id' => $position->getId()]);
                }
            } elseif($form->get('delete')->isClicked()) {
                if($errorLinks == false){
                    try {
                        $entityManager->remove($position);
                        $entityManager->flush();
                        $this->addFlash('warning', 'Le poste a été supprimé avec succès.');
                        return $this->redirectToRoute('app_administration_job');
                    } catch (ForeignKeyConstraintViolationException $e) {
                        $this->addFlash('error', 'Impossible de supprimer ce poste : il est encore utilisé par un ou plusieurs utilisateurs.');
                        return $this->redirectToRoute('app_administration_detail_job', ['id' => $position->getId()]);
                    }
                }else{
                    $this->addFlash('error', 'Impossible de supprimer ce poste : il est encore utilisé par un ou plusieurs utilisateurs.');
                    return $this->redirectToRoute('app_administration_detail_job', ['id' => $position->getId()]);
                }
            }
        }
    
        return $this->render('manager/admin/job/detail_job.html.twig', [
            'page' => 'administration-job',
            'form' => $form->createView(),
        ]);
    }
}