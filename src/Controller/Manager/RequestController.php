<?php

namespace App\Controller\Manager;

use App\Form\FilterManagerFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as RequestFondation;
use App\Form\FilterRequestPendingFormType;
use Symfony\Bundle\SecurityBundle\Security; 
use App\Entity\Request ;
use App\Entity\User;
use App\Entity\Person;
use App\Repository\UserRepository;
use App\Repository\RequestRepository ; 
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta ;
use App\Form\FilterHistoRequestType;
use App\Repository\PersonRepository;
use App\Form\RequestStatusFormType;

class RequestController extends AbstractController
{
    // PAGE DES DEMANDES EN ATTENTE
    #[Route('/request-pending', name: 'app_request_pending')]
    public function viewRequestPending(Security $security, RequestFondation $request, UserRepository $userRepository, RequestRepository $requestRepository, PersonRepository $personRepository): Response
    {
        // Récupérez l'utilisateur connecté
        $user = $security->getUser();
        // Vérifiez si l'utilisateur est une instance de User avant d'appeler getId()
        if ($user instanceof User) {
            $userId = $user->getId();
        } else {
            throw new \LogicException('L\'utilisateur connecté n\'est pas valide.');
        }

        $form = $this->createForm(FilterRequestPendingFormType::class);
        $form->handleRequest($request);

        // Récupérez les collaborateurs du manager
        $collaborators = $userRepository->findCollaboratorsByManager($userId);

        $allRequests = [];

        foreach ($collaborators as $collaboratorData) {
            $collaboratorId = $collaboratorData['person_id'];
            $collaborator = $personRepository->find($collaboratorId);
            $requests = $requestRepository->getTeamRequest($collaboratorId);
        
            $allRequests[] = [
                'collaborator' => $collaborator,
                'requests' => $requests,
            ];
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Logique de traitement des données du formulaire
            // Tu peux ensuite filtrer les demandes ici avec $form->getData()
        }

        return $this->render('manager/request_pending.html.twig', [
            'page' => 'request-pending',
            'form' => $form->createView(),
            'requests' => $allRequests,
        ]);
    }

    //PAGE DETAILS DES DEMANDES EN ATTENTE
    #[Route('/detail-request-pending/{id}', name: 'app_detail_request_pending')]
    public function viewDetailRequestPending(Security $security, int $id, RequestRepository $requestRepository, RequestFondation $request,): Response
    {
        // Récupérez l'utilisateur connecté
        $user = $security->getUser();
        // Vérifiez si l'utilisateur est une instance de User avant d'appeler getId()
        if ($user instanceof User) {
            $userId = $user->getId();
        } else {
            throw new \LogicException('L\'utilisateur connecté n\'est pas valide.');
        }

        $requestLoaded = $requestRepository->find($id);
        if (!$requestLoaded) {
            throw $this->createNotFoundException('La demande n\'existe pas.');
        }

        $form = $this->createForm(RequestStatusFormType::class);
        $form->handleRequest($request);

        return $this->render('manager/detail_request_pending.html.twig', [
            'page' => 'detail-request-pending',
            'request' => $requestLoaded,
            'form' => $form->createView(),
        ]);
    }

    //PAGE HISTORIQUE DES DEMANDES
    #[Route('/history-request', name: 'app_history_request_manager', methods:['GET', 'POST'])]
    public function viewRequestHistory(RequestFondation $request, RequestRepository $repository, int $page = 1 ): Response
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
            'answer' => $request->query->get('answer'),
        ];
        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $filters = array_merge($filters, $formData);    
            //Recherche avec les filtres
            $order = $filters['totalnbdemande'] ?? '';
            $query = $repository->searchRequest($filters, $order);
        }else{
            $query = $repository->createQueryBuilder('r')->getQuery();

        }
            
        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        try{
            $pagerfanta->setCurrentPage($page);
        }
        catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }

        return $this->render('manager/history_request.html.twig', [
            'page' => 'history-request',
            'form' => $form->createView(),
            'requests' => $pagerfanta->getCurrentPageResults(),
            'pager' => $pagerfanta,
            'filters' => $filters,
        ]);
    }
}
