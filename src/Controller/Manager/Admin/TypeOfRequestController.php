<?php

namespace App\Controller\Manager\Admin;

use App\Form\FilterAdminDemandeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\RequestType;
use App\Form\RequestTypeForm;
use App\Repository\RequestTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter as ORMQueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

class TypeOfRequestController extends AbstractController
{
    //PAGE TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-type-de-demande/{page}', name: 'app_administration_type_of_request', methods: ['GET', 'POST'])]
    public function viewTypeOfRequest(Request $request, RequestTypeRepository $requestTypeRepository, int $page = 1): Response
    {
        $form = $this->createForm(FilterAdminDemandeFormType::class);
        $form->handleRequest($request);

        $filters = [
            'name'=> $request->query->get('name'),
            'orderBy'=> $request->query->get('orderBy'),
        ];

        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        $order = $filters['totalnbdemande'] ?? '';

        $query = $requestTypeRepository->searchTypeOfRequest($filters, $order);

        //PAGINATION
        $adapter = new ORMQueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);

        try {
            if($form->isSubmitted()) {
                $page = 1;
            }
            $pagerfanta->setCurrentPage($page);
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
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
    #[Route('/administration-ajouter-type-de-demande', name: 'app_administration_add_type_of_request', methods: ['POST'])]
    public function addTypeOfRequest(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(RequestTypeForm::class, null, [
            'isAdd' => true,
        ]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $requestType = $form->getData();
            $entityManagerInterface->persist($requestType);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Le type de demande a été ajouté avec succès.');
            return $this->redirectToRoute('app_administration_type_of_request');
        }

        return $this->render('manager/admin/type-of-request/add_type_of_request.html.twig', [
            'page' => 'administration-type-de-demande',
            'form' => $form->createView(),
        ]);
    }

    //PAGE DETAIL TYPE DE DEMANDE VIA ADMINISTRATION MANAGER
    #[Route('/administration-detail-type-de-demande/{id}', name: 'app_administration_detail_type_of_request', methods: ['GET', 'POST'])]
    public function editRequestType(RequestType $typeDemande, Request $request, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $form = $this->createForm(RequestTypeForm::class, $typeDemande, [
            'isAdd' => false
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                try {
                    $entityManager->remove($typeDemande);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le type de demande a été supprimé avec succès.');
                    return $this->redirectToRoute('app_administration_type_of_request');
                } catch (ForeignKeyConstraintViolationException $e) {
                    $this->addFlash('error', 'Impossible de supprimer ce type de demande : il est encore utilisé par un ou plusieurs utilisateurs.');
                    return $this->redirectToRoute('app_administration_detail_type_of_request', ['id' => $typeDemande->getId()]);
                }


            } else {
                $entityManager->flush();
                $this->addFlash('success', 'Le type de demande a été modifié avec succès.');
            }
        
            return $this->redirectToRoute('app_administration_type_of_request');
        }
    
        return $this->render('manager/admin/type-of-request/detail_type_of_request.html.twig', [
            'form' => $form->createView(),
            'page' => 'administration-type-de-demande',
            'requestType' => $typeDemande,
        ]);
    }
}
