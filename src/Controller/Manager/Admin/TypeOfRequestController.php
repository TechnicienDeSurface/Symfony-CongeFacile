<?php

namespace App\Controller\Manager\Admin;

use App\Form\FilterAdminDemandeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\RequestType ; 
use App\Form\RequestTypeForm ;
use App\Repository\RequestTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter as ORMQueryAdapter;
use Pagerfanta\Pagerfanta;

class TypeOfRequestController extends AbstractController
{
    //PAGE TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-type-de-demande/{page}', name: 'app_administration_type_of_request')]
    public function viewTypeOfRequest(Request $request, RequestTypeRepository $requestTypeRepository, int $page = 1): Response
    {
        $form = $this->createForm(FilterAdminDemandeFormType::class);
        $form->handleRequest($request);

        $filters = [
            'name'         => $request->query->get('name'),
            'totalnbdemande'    => $request->query->get('totalnbdemande'),
        ];

        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        // Recherche dans le repository avec les filtres
        $query = $requestTypeRepository->searchTypeOfRequest($filters);
        
        // Pagination avec QueryAdapter
        $adapter = new ORMQueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);

        try{
            $pagerfanta->setCurrentPage($page);
        }
        catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }
        return $this->render('manager/admin/type-of-request/type_of_request.html.twig', [
            'page' => 'administration-type-de-demande',
            'form' => $form->createView(),
            'pager' => $pagerfanta,
            'requesttype' => $pagerfanta->getCurrentPageResults(),
            'filters' => $filters,
        ]);
    }

    //PAGE AJOUTER TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-type-de-demande', name: 'app_administration_ajouter_type_of_request')]
    public function addTypeOfRequest(): Response
    {
        return $this->render('manager/admin/type-of-request/add_type_of_request.html.twig', [
            'page' => 'administration-type-de-demande',
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
            'page' => 'administration-type-de-demande',
        ]);
    }

    //SUPPRIMER MANAGEMENTS ET SERVICES VIA L'ADMINISTRATION DU PORTAIL MANAGER
    //#[Route('/administration-supprimer-type-de-demande/{id}', name: 'app_administration_supprimer_type_of_request', methods: ['POST', 'DELETE'])]
}
