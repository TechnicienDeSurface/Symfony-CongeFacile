<?php

namespace App\Controller\Manager\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\RequestType ; 
use App\Form\RequestTypeForm ; 
use Doctrine\ORM\EntityManagerInterface;

class TypeOfRequestController extends AbstractController
{
    //PAGE TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-type-de-demande', name: 'app_administration_type_of_request')]
    public function viewTypeOfRequest(): Response
    {
         $demandes = [
            ['nom' => 'Congé annuel', 'nb' => 15],
            ['nom' => 'Congé maladie', 'nb' => 8],
            ['nom' => 'Congé maternité', 'nb' => 5],
            ['nom' => 'Congé paternité', 'nb' => 3],
            ['nom' => 'Congé sans solde', 'nb' => 2],
            ['nom' => 'Congé sabbatique', 'nb' => 1],
            ['nom' => 'Congé parental', 'nb' => 4],
            ['nom' => 'Congé de formation', 'nb' => 6],
            ['nom' => 'Congé de déménagement', 'nb' => 2],
            ['nom' => 'Congé de mariage', 'nb' => 3],
            // Ajoutez d'autres demandes ici
        ];
        return $this->render('manager/admin/type-of-request/type_of_request.html.twig', [
            'page' => 'administration-type-of-request',
            'demandes' => $demandes, 
        ]);
    }

    //PAGE AJOUTER TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-type-de-demande', name: 'app_administration_ajouter_type_of_request')]
    public function addTypeOfRequest(): Response
    {
        return $this->render('manager/admin/type-of-request/add_type_of_request.html.twig', [
            'page' => 'administration-ajouter-type-de-demande',
        ]);
    }

    //PAGE DETAIL TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-detail-type-de-demande/{id}', name: 'app_administration_detail_type_of_request')]
    public function editRequestType(RequestType $typeDemande, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RequestTypeForm::class, $typeDemande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('some_route');
        }

        return $this->render('type_demande/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //SUPPRIMER MANAGEMENTS ET SERVICES VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-type-de-demande/{id}', name: 'app_administration_supprimer_type_of_request', methods: ['POST', 'DELETE'])]
}
