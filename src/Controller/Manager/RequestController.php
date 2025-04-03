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
use App\Form\FilterManagerHistoDemandeType;

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
    #[Route('/history-request', name: 'app_history_request')]
    public function viewRequestHistory(Request $request, RequestRepository $repository, int $page = 1 ): Response
    {
        $requests = $repository->findBy([],[]) ;

        $form = $this->createForm(FilterManagerHistoDemandeType::class);
        $form->handleRequest($request);

        return $this->render('manager/history_request.html.twig', [
            'page' => 'history-request',
            'requests' => $requests,
            'form' => $form,  
        ]);
    }
}
