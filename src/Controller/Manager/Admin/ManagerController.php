<?php

namespace App\Controller\Manager\Admin;

use App\Entity\User; 
use App\Form\UserType ; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository; 
use App\Repository\PersonRepository;
use Doctrine\Persistence\ManagerRegistry;

class ManagerController extends AbstractController
{
    //PAGE MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-manager', name: 'app_administration_manager')]
    public function viewManager(Request $request, PersonRepository $repository, UserRepository $UserRepository): Response
    {
        $form = $this->createForm(\App\Form\FilterManagerTeamFormType::class);
        $form->handleRequest($request);

        $Managers = $UserRepository->findAllManagers();
        
        $personIds = [];
        $userIds = [];

        foreach ($Managers as $manager) {
            $personIds[] = $manager['person_id'];
            $userIds[] = $manager['user_id'];
        }

        $persons = [];
        foreach ($personIds as $personId) {
            $person = $repository->find($personId);
            if ($person) {
                $persons[] = $person;
            }
        }

        dump($persons);

        return $this->render('manager/admin/manager/manager.html.twig', [
            'page' => 'administration-manager',
            'managers' => $Managers,
            'persons' => $persons,
            'form' => $form->createView(),
        ]);
    }

    //PAGE AJOUTER MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-manager', name: 'app_administration_ajouter_manager')]
    public function addManager(ManagerRegistry $registry,Request $request): Response
    {
        $manager = new User() ; 
        $form = $this->createForm(UserType::class, $manager) ; 
        $form->handleRequest($request) ; 
        
        if($form->isSubmitted())
        {
            if($form->isValid()){
                try{
                    $registry->getManager()->persist($manager) ; 
                    $registry->getManager()->flush();
                    $this->addFlash('success', 'Success to add manager') ; 
                }catch(\Exception $e ){
                    $this->addFlash('error', 'Error to add manager') ; 
                }
            }
        }
        
        return $this->render('manager/admin/manager/add_manager.html.twig', [
            'page' => 'administration-ajouter-manager',
        ]);
    }

    // //PAGE DETAIL MANAGER VIA ADMINISTRATION MANAGER
    // #[Route('/administration-detail-manager/{id}', name: 'app_administration_detail_manager')]
    // public function editManager(PersonRepository $repository, int $id, ManagerRegistry $registry,Request $request): Response
    // {
    //     $manager = $repository->find($id);
    //     if(!$manager){
    //         throw $this->createNotFoundException('No category found for id ' . $id);
    //     }
    //     $form = $this->createForm(UserType::class, $manager);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted()) {
    //         if ($form->isValid()) {
    //             $request->cookies->all();
    //             // Si valide : j'enregistre les données dans la BDD.
    //             $registry->getManager()->flush();
    //         } else {
    //             // Sinon j'affiche un message d'erreur
    //         }
    //     }
    //     return $this->render('manager/admin/manager/detail_manager.html.twig', [
    //         'page' => 'administration-detail-manager',
    //     ]);
    // }

    #[Route('/administration-detail-manager', name: 'app_administration_detail_manager')]
    public function detailManager(): Response
    {
        return $this->render('manager/admin/manager/detail_manager.html.twig', [
            'page' => 'administration-detail-manager',
        ]);
    }

    //SUPPRIMER MANAGER VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-supprimer-manager/{id}', name: 'app_administration_supprimer_manager', methods: ['POST', 'DELETE'])]
    public function delete(int $id, UserRepository $productRepository, UserRepository $repository, ManagerRegistry $registry): Response 
    {
        $manager = $repository->find($id);

        if (!$manager) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        $entityManager = $registry->getManager();
        $productCount = $productRepository->countByCategory($manager->getId());

        if ($productCount > 0) {
            $this->addFlash('error', 'Impossible de supprimer ce manager car elle est utilisée par des produits.');
            return $this->redirectToRoute('category/list');
        } else {
            $entityManager->remove($manager);
            $entityManager->flush();
        }
        
        return $this->render('manager/admin/manager/manager.html.twig', [
            'page' => 'administration-manager',
        ]);
    }
}
