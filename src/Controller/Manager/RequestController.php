<?php

namespace App\Controller\Manager;

use App\Form\FilterManagerFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RequestRepository ; 
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FilterHistoRequestType;

class RequestController extends AbstractController
{
    //PAGE DES DEMANDES EN ATTENTE
    #[Route('/request-pending', name: 'app_request_pending')]
    public function viewRequestPending(): Response
    {
        return $this->render('manager/request_pending.html.twig', [
            'page' => 'request-pending',
        ]);
    }

    //PAGE DETAILS DES DEMANDES EN ATTENTE
    #[Route('/detail-request-pending/{id}', name: 'app_detail_request_pending')]
    public function viewDetailRequestPending(): Response
    {
        return $this->render('manager/detail_request_pending.html.twig', [
            'page' => 'detail-request-pending',
        ]);
    }

    //PAGE HISTORIQUE DES DEMANDES
    #[Route('/history-request', name: 'app_history_request', methods:['GET', 'POST'])]
    public function viewRequestHistory(Request $request, RequestRepository $repository, int $page = 1 ): Response
    {
        $requests = $repository->findBy([],[]) ;

        $form = $this->createForm(FilterHistoRequestType::class);
        $form->handleRequest($request);

        $filters = json_decode($request->getContent(), true) ?? [
            'request_type'=>$request->query->get('request_type'),
            'start_at'=> $request->query->get('start_at'),
            'end_at'=> $request->query->get('end_at'),
            'totalnbdemande' => $request->query->get('totalnbdemande'),
            'collaborator' => $request->query->get('collaborator'),
        ];
        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $filters = array_merge($filters, $formData);    
            
        }

        $order = $filters['totalnbdemande'] ?? '';

        // Recherche dans le repository avec les filtres
        $query = $repository->searchTypeOfRequest($filters, $order);
        
        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        try{
            $pagerfanta->setCurrentPage($page);
            // dd($pagerfanta) ; 
        }
        catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
            dd('no') ; 
        }

        // dd($requests) ; 
        return $this->render('manager/history_request.html.twig', [
            'page' => 'history-request',
            'form' => $form->createView(),
            'requests' => $pagerfanta->getCurrentPageResults(),
            'pager' => $pagerfanta,
            'filters' => $filters,
        ]);
    }
}
