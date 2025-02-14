<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PersonRepository  ; 
use Symfony\Component\HttpFoundation\Request;
use App\Form\PersonType ; 
use Doctrine\Persistence\ManagerRegistry;

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
    public function viewRequestHistory(): Response
    {
        return $this->render('manager/history_request.html.twig', [
            'page' => 'history-request',
        ]);
    }

    //PAGE MISE à JOUR 
    #[Route('/update-manager',name: 'update_manager' )]
    public function edit(int $id, ManagerRegistry $registry, PersonRepository $repository, Request $request): Response
    {
        $manager = $repository->find($id); 
        if(!$manager)
        {
            throw $this->createNotFoundException('Pas de manager trouvé') ; 
        }
        $form = $this->createForm(PersonType::class, $manager); 
        $form->handleRequest($request); 

        if($form->isSubmitted()){
            if($form->isValid()){
                $request->cookies->all(); 
                $registry->getManager()->flush();  
            }
        }
        return $this->render('manager/detail_request_pending.html.twig', [
            'page'=>'update-request',
            'form' => $form,  
        ]); 
    }
}
