<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use App\Repository\PersonRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ManagementServiceController extends AbstractController
{
    //PAGE MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-management-service', name: 'app_administration_management_service', methods: ['GET', 'POST'])]
    public function viewManagementService(Request $request, EntityManagerInterface $entityManager, int $page = 1): Response
    {
        $departments = $entityManager->getRepository(Department::class)->createQueryBuilder('d')
        ->orderBy('d.name', 'ASC')
        ->getQuery();
        
        //PAGINATION
        $adapter = new QueryAdapter($departments);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);

        try {
            $pagerfanta->setCurrentPage($page);
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }


        return $this->render('manager/admin/management-service/management_service.html.twig', [
            'page' => 'administration-management-service',
            'departments' => $pagerfanta->getCurrentPageResults(),
            'pager' => $pagerfanta,
        ]);
    }

    //PAGE AJOUTER MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-ajouter-management-service', name: 'app_administration_ajouter_management_service', methods: ['GET', 'POST'])]
    public function addManagementService(DepartmentRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $department = new Department();     
        $departments = $repository->findBy([],[]);
        $verif = false; 
        $form = $this->createForm(DepartmentType::class, $department, [
            'submit_label' => 'Ajouter',
         ]);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
             if (empty($department->getName())) {
                 $this->addFlash('error', 'Erreur : le champ ne peut pas être vide.');
             } else {
                 foreach ($departments as $row) {
                     if ($department->getName() == $row->getName()) {
                         $verif = true;
                     };
                 }
                 if ($verif === true) {
                     $this->addFlash('error', 'Erreur : ce département existe déjà.');
                 } else {
                     $entityManager->persist($department);
                     $entityManager->flush();
                     $this->addFlash('success', 'Le département a été ajouté avec succès.');
                     return $this->redirectToRoute('app_administration_management_service');
                 }
             }
         }

        return $this->render('manager/admin/management-service/add_management_service.html.twig', [
            'page' => 'administration-ajouter-management-service',
            'form' => $form, 
        ]);
    }



    //PAGE DETAIL MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-detail-management-service/{id}', name: 'app_administration_detail_management_service', methods: ['GET', 'POST'])]
    public function editManagementService(PersonRepository $repository, Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {

        if (!$department) {
            throw $this->createNotFoundException('Le département n\'existe pas.');
        }

        // Créer le formulaire
        $form = $this->createForm(DepartmentType::class, $department, ['submit_label' => 'Mettre à jour']);

        $form->handleRequest($request);
        $errorLinks = $repository->findPersonByDepartment($department->getId());
        // Si le formulaire est soumis
        if ($form->isSubmitted()) {
            // Action de mise à jour
            if ($form->get('submit')->isClicked() && $form->isValid()) {
                $entityManager->flush(); // Mettre à jour le département
                $this->addFlash('success', 'Le département a été mis à jour.');
                return $this->redirectToRoute('app_administration_detail_management_service', ['id' => $department->getId()]);
            }

            // Action de suppression
            if ($form->get('delete')->isClicked()) {
                if($errorLinks == false){
                    $entityManager->remove($department); // Supprimer le département
                    $entityManager->flush(); // Appliquer la suppression
                    $this->addFlash('warning', 'Le département a été supprimé.');
                    return $this->redirectToRoute('app_administration_management_service'); // Rediriger vers la liste des départements
                }else{
                    $this->addFlash('error', 'Impossible de supprimer ce département, des personnes y sont enregistrés.');
                    return $this->redirectToRoute('app_administration_management_service');
                }
            }
        }

        return $this->render('manager/admin/management-service/detail_management_service.html.twig', [
            'page' => 'administration-management-service',
            'department' => $department,
            'form' => $form->createView(),
        ]);
 
        
    }
}
