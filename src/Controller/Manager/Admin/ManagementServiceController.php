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
    #[Route('/administration-ajouter-management-service', name: 'app_administration_ajouter_management_service')]
    public function addManagementService(Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name'));

            if (!empty($name)) {
                $department = new Department();
                $department->setName($name);

                $entityManager->persist($department);
                $entityManager->flush();

                $this->addFlash('success', 'Le département a été créé avec succès.');

                return $this->redirectToRoute('app_administration_management_service');
            } else {
                $this->addFlash('error', 'Le nom du département ne peut pas être vide.');
            }
        }


        return $this->render('manager/admin/management-service/add_management_service.html.twig', [
            'page' => 'administration-ajouter-management-service',
        ]);
    }



    //PAGE DETAIL MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-detail-management-service/{id}', name: 'app_administration_detail_management_service', methods: ['GET', 'POST'])]
    public function editManagementService(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {

        if (!$department) {
            throw $this->createNotFoundException('Le département n\'existe pas.');
        }

        // Créer le formulaire
        $form = $this->createForm(DepartmentType::class, $department);

        $form->handleRequest($request);

        // Si le formulaire est soumis
        if ($form->isSubmitted()) {
            // Action de mise à jour
            if ($form->get('edit')->isClicked() && $form->isValid()) {
                $entityManager->flush(); // Mettre à jour le département
                $this->addFlash('success', 'Le département a été mis à jour.');
                return $this->redirectToRoute('app_administration_detail_management_service', ['id' => $department->getId()]);
            }

            // Action de suppression
            if ($form->get('delete')->isClicked()) {
                $entityManager->remove($department); // Supprimer le département
                $entityManager->flush(); // Appliquer la suppression
                $this->addFlash('warning', 'Le département a été supprimé.');
                return $this->redirectToRoute('app_administration_management_service'); // Rediriger vers la liste des départements
            }
        }

        return $this->render('manager/admin/management-service/detail_management_service.html.twig', [
            'page' => 'administration-detail-management-service',
            'department' => $department,
            'form' => $form->createView(),
        ]);
 
        
    }
}
