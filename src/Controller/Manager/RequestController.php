<?php

namespace App\Controller\Manager;

use App\Form\FilterRequestHistoryFormType;
use App\Repository\RequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

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

    //PAGE HISTORIQUE DES DEMANDES EN MODE "MANAGER"
    #[Route('/history-request-manager/{page}', name: 'app_history_request_manager', methods: ['GET', 'POST'])]
    public function viewRequestHistory(Request $request, RequestRepository $requestRepository, int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $form = $this->createForm(FilterRequestHistoryFormType::class,null,[
            'is_manager' => true,
        ]);

        $form->handleRequest($request);

        $filters = [
            'request_type'     => $request->query->get('requesttype'),
            'collaborator'     => $request->query->get('collaborator'),
            'start_at'         => $request->query->get('startat'),
            'end_at'           => $request->query->get('endat'),
            'nbdays'           => $request->query->get('nbdays'),
            'answer'           => $request->query->get('answer'),
        ];
        

        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        $order = $filters['nbdays'] ?? '';

        // Recherche dans le repository avec les filtres
        $query = $requestRepository->HistoryRequestfindByFilters($filters, $order);
        
        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);

        try{
            $pagerfanta->setCurrentPage($page);
        }
        catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }

        return $this->render('manager/history_request.html.twig', [
            'page' => 'history-request-manager',
            'pager' => $pagerfanta,
            'form' => $form->createView(),
            'filters' => $filters,
            'request' => $pagerfanta->getCurrentPageResults(),
        ]);
    }
    
}
