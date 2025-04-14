<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FilterRequestPendingFormType;

class RequestController extends AbstractController
{
    // PAGE DES DEMANDES EN ATTENTE
    #[Route('/request-pending', name: 'app_request_pending')]
    public function viewRequestPending(Request $request): Response
    {
        $form = $this->createForm(FilterRequestPendingFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Logique de traitement des données du formulaire
            $data = $form->getData();
            // Tu peux ensuite filtrer les demandes ici avec $data
        }

        return $this->render('manager/request_pending.html.twig', [
            'page' => 'request-pending',
            'form' => $form->createView(),
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
    public function viewRequestHistory(): Response
    {
        return $this->render('manager/history_request.html.twig', [
            'page' => 'history-request',
        ]);
    }
}
