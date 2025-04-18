<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as RequestFondation;
use App\Form\FilterRequestPendingFormType;
use Symfony\Bundle\SecurityBundle\Security; 
use App\Entity\Request;
use App\Entity\User;
use App\Entity\Person;
use App\Repository\UserRepository;

class RequestController extends AbstractController
{
    // PAGE DES DEMANDES EN ATTENTE
    #[Route('/request-pending', name: 'app_request_pending')]
    public function viewRequestPending(Security $security, RequestFondation $request, UserRepository $userRepository): Response
    {
        // Récupérez l'utilisateur connecté
        $user = $security->getUser();
        // Vérifiez si l'utilisateur est une instance de User avant d'appeler getId()
        if ($user instanceof User) {
            $userId = $user->getId();
        } else {
            throw new \LogicException('L\'utilisateur connecté n\'est pas valide.');
        }

         // Récupérez les collaborateurs du manager
        $collaborators = $userRepository->findCollaboratorsByManager($userId);
        
        $form = $this->createForm(FilterRequestPendingFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Logique de traitement des données du formulaire
            // Tu peux ensuite filtrer les demandes ici avec $form->getData()
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
