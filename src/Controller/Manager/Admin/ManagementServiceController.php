<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Department;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ManagementServiceController extends AbstractController
{
    //PAGE MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
    #[Route('/administration-management-service', name: 'app_administration_management_service')]
    public function viewManagementService(EntityManagerInterface $entityManager): Response
    {
        $departments = $entityManager->getRepository(Department::class)->findAll();



        return $this->render('manager/admin/management-service/management_service.html.twig', [
            'page' => 'administration-management-service',
            'departments' => $departments,
        ]);
    }

    //PAGE AJOUTER MANAGEMENTS ET SERVICES VIA ADMINISTRATION MANAGER
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
    #[Route('/administration-detail-management-service/{id}', name: 'app_administration_detail_management_service', methods: ['GET', 'POST'])]
    public function editManagementService(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {

        if (!$department) {
            throw $this->createNotFoundException('Le département n\'existe pas.');
        }

        if ($request->isMethod('POST')) {
            $newName = $request->request->get('name');

            if ($request->request->has('edit')) {
                // Modifier le nom du département
                $department->setName($newName);
                $entityManager->flush();
                $this->addFlash('success', 'Le département a été mis à jour.');

                return $this->redirectToRoute('app_administration_detail_management_service', [
                    'id' => $department->getId(),
                ]);
            }

            if ($request->request->has('delete')) {
                // Supprimer le département
                $entityManager->remove($department);
                $entityManager->flush();
                $this->addFlash('warning', 'Le département a été supprimé.');

                return $this->redirectToRoute('app_administration_management_service');
            }
        }

        return $this->render('manager/admin/management-service/detail_management_service.html.twig', [
            'page' => 'administration-detail-management-service',
            'department' => $department,
        ]);







        
    }

    //SUPPRIMER MANAGEMENTS ET SERVICES VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-management-service/{id}', name: 'app_administration_supprimer_management-service', methods: ['POST', 'DELETE'])]
}
