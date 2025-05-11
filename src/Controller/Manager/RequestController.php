<?php

namespace App\Controller\Manager;

use Pagerfanta\Adapter\ArrayAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as RequestFondation;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FilterRequestPendingFormType;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RequestRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use App\Form\FilterHistoRequestType;
use App\Repository\PersonRepository;
use App\Form\RequestStatusFormType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RequestController extends AbstractController
{
    // PAGE DES DEMANDES EN ATTENTE
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/request-pending/{page}', name: 'app_request_pending')]
    public function viewRequestPending(Security $security, RequestFondation $request, RequestRepository $requestRepository, PersonRepository $personRepository, int $page = 1): Response
    {
        // Récupérez l'utilisateur connecté
        /** @var App\Entity\User $user */
        $user = $security->getUser();
        // Vérifiez si l'utilisateur est une instance de User avant d'appeler getId()
        if ($user instanceof User) {
            $person = $user->getPerson();
        } else {
            throw new \LogicException('L\'utilisateur connecté n\'est pas valide.');
        }

        $filters = [];
        $allCollaborators = [];

        $department = $person->getDepartment();
        $departmentId = $department->getId();

        $collaborators = [];
        $collaborators = $personRepository->getPersonByIdDepartment($departmentId);

        $allRequests = []; // Debugging line
        $daysWorking = []; 
        foreach ($collaborators as $collaboratorData) {
            $collaboratorId = $collaboratorData->getId();
            $collaborator = $personRepository->find($collaboratorId);
            $requests = $requestRepository->findRequestPendingByManager($user->getId());

            foreach ($requests as $requestsFiltered) {
                // Calcul du nombre de jours ouvrés pour cette demande
                if ($requestsFiltered !== null) {
                    $nbDaysWorking = $requestRepository->getWorkingDays($requestsFiltered->getStartAt(), $requestsFiltered->getEndAt());
                } else {
                    $nbDaysWorking = 0; // Default value or handle the case appropriately
                }

                // Ajouter les jours ouvrés à un tableau pour ce collaborateur
                $daysWorking[] = [
                    'request' => $requestsFiltered,
                    'nbDaysWorking' => $nbDaysWorking
                ];
            }
            $allCollaborators[] = $collaborator; // Ajout de la liste des collaborateurs

            $allRequests[] = [
                'collaborator' => $collaborator,
                'requests' => $requests,
                'daysWorking' => $daysWorking,
            ];
        }

        // Créer le formulaire en passant les collaborateurs comme option
        $form = $this->createForm(FilterRequestPendingFormType::class, null, [
            'collaborators' => $allCollaborators,
        ]);

        $form->handleRequest($request); // Traiter la requête du formulaire


        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $allRequests = [];

            $filters = [
                'request_type'      => $formData['request_type'] ?? null,
                'collaborator'      => $formData['collaborator'] ?? null,
                'start_at'          => $formData['start_at'] ?? null,
                'end_at'            => $formData['end_at'] ?? null,
                'created_at'        => $formData['created_at'] ?? null,
            ];

            if (!empty($formData['collaborator'])) {
                $filters['collaborator'] = $formData['collaborator'];
            }

            if (!empty($formData['start_at'])) {
                $filters['start_at'] = $formData['start_at'];
            }

            if (!empty($formData['end_at'])) {
                $filters['end_at'] = $formData['end_at'];
            }

            if (!empty($formData['created_at'])) {
                $filters['created_at'] = $formData['created_at'];
            }

            if (!empty($formData['request_type'])) {
                $filters['request_type'] = $formData['request_type'];
            }

            if (!empty($formData['answer'])) {
                $filters['answer'] = $formData['answer'];
            }

            if (!empty($formData['nbdays'])) {
                $order = $formData['nbdays'];
            }
            
            $filteredRequests = $requestRepository->findRequestPendingByFilters($user->getId(), $filters, $order);

            foreach ($collaborators as $collaboratorData) {
                $collaboratorId = $collaboratorData->getId();
                $collaborator = $personRepository->find($collaboratorId);

                // Filtrer les requêtes pour ce collaborateur
                $requestsForCollaborator = array_filter($filteredRequests, function ($request) use ($collaboratorId) {
                    return $request->getCollaborator()->getId() === $collaboratorId;
                });
                // Recalculer daysWorking pour chaque request filtrée
                $daysWorking = [];
                foreach ($filteredRequests as $requestsFiltered) {
                    $nbDaysWorking = $requestRepository->getWorkingDays(
                        $requestsFiltered->getStartAt(),
                        $requestsFiltered->getEndAt()
                    );

                    $daysWorking[] = [
                        'request' => $requestsFiltered,
                        'nbDaysWorking' => $nbDaysWorking,
                    ];
                }

                // Ajouter les résultats au tableau dans la même structure qu'au début
                $allRequests[] = [
                    'collaborator' => $collaborator,
                    'requests' => $filteredRequests,
                    'daysWorking' => $daysWorking,
                ];
            }
        }

        $adapter = new ArrayAdapter(is_array($collaborators) ? $collaborators : [$collaborators]);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);

        try {
            $pagerfanta->setCurrentPage($page);
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }

        return $this->render('manager/request_pending.html.twig', [
            'pager' => $pagerfanta,
            'page' => 'request-pending',
            'form' => $form->createView(),
            'filters' => $filters,
            'requests' => $allRequests,
        ]);
    }

    //PAGE DETAILS DES DEMANDES EN ATTENTE
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/detail-request-pending', name: 'app_detail_request_pending')]
    public function viewDetailRequestPending(SessionInterface $session, Security $security, RequestRepository $requestRepository, Request $requestHttp, EntityManagerInterface $entityManager): Response
    {
        // Récupérez l'utilisateur connecté
        $user = $security->getUser();
        // Vérifiez si l'utilisateur est une instance de User avant d'appeler getId()
        if ($user instanceof User) {
            $userId = $user->getId();
        } else {
            throw new \LogicException('L\'utilisateur connecté n\'est pas valide.');
        }

        $form = $this->createForm(RequestStatusFormType::class);
        $form->handleRequest($requestHttp);

        $id = $requestHttp->request->get('id');
        if ($id == null || empty($id)) {
            // juste avant le find :
            $id = $session->get('pending_request_id');
        }

        $requestLoaded = $requestRepository->find($id);
        /** @var \DateTimeInterface $startInterface */
        $startInterface = $requestLoaded->getStartAt();
        /** @var \DateTimeInterface $endInterface */
        $endInterface   = $requestLoaded->getEndAt();

        // Si tu utilises DateTimeImmutable partout dans Doctrine, remplace createFromMutable par createFromInterface.
        $start = \DateTime::createFromInterface($startInterface);
        $end   = \DateTime::createFromInterface($endInterface);

        // Normaliser l’ordre
        if ($end < $start) {
            [$start, $end] = [$end, $start];
        }

        $period = new \DatePeriod(
            (clone $start)->setTime(0, 0, 0),
            new \DateInterval('P1D'),
            (clone $end)->setTime(0, 0, 0)->modify('+1 day')
        );

        $totalBusinessSeconds = 0;
        foreach ($period as $day) {
            $dow = (int) $day->format('w');
            if ($dow === 0 || $dow === 6) {
                continue;
            }

            $workStart = (clone $day)->setTime(9, 0, 0);
            $workEnd   = (clone $day)->setTime(17, 0, 0);

            $intervalStart = $start > $workStart ? $start : $workStart;
            $intervalEnd   = $end   < $workEnd   ? $end   : $workEnd;

            if ($intervalEnd > $intervalStart) {
                $totalBusinessSeconds += $intervalEnd->getTimestamp()
                                        - $intervalStart->getTimestamp();
            }
        }

        $daysFloat      = $totalBusinessSeconds / 28800;
        $nbDaysWorking  = round($daysFloat * 2) / 2;

        if (!$requestLoaded) {
            throw $this->createNotFoundException('La demande n\'existe pas.');
        }
        
    
        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $formData = $form->getData();
                
                $id = $requestHttp->request->get('id');

                if (!$id) {
                    throw new \InvalidArgumentException('ID manquant.');
                }

                $requestLoaded = $requestRepository->find($id);
                
                if (!$requestLoaded) {
                    throw $this->createNotFoundException('La demande n\'existe pas.');
                }

                // Vérifie le bouton cliqué
                if ($form->get('accept')->isClicked()) {
                    $answer = true;
                } elseif ($form->get('refuse')->isClicked()) {
                    $answer = false;
                } else {
                    throw new \LogicException('Aucun bouton valide cliqué.');
                }
                $requestLoaded->setAnswer($answer);
                $requestLoaded->setAnswerComment($formData['answer']);
                $requestLoaded->setAnswerAt(new \DateTime());
                $entityManager->flush();
                $this->addFlash('sucess','Réponse enregistré');
            }catch(\Exception $e){
                $this->addFlash('error','Erreur lors de la réponse');
            }
            return $this->redirectToRoute('app_request_pending');
        }

        return $this->render('manager/detail_request_pending.html.twig', [
            'page' => 'request-pending',
            'request' => $requestLoaded,
            'requestId' => $id,
            'form' => $form->createView(),
            'nbDaysWorking' => $nbDaysWorking,
        ]);
    }

    //PAGE HISTORIQUE DES DEMANDES
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/history-request/{page}', name: 'app_history_request_manager', methods: ['GET', 'POST'])]
    public function viewRequestHistory(RequestFondation $request, RequestRepository $repository, int $page = 1): Response
    {
        $requests = $repository->findBy([], []);
        $form = $this->createForm(FilterHistoRequestType::class);
        $form->handleRequest($request);

        $filters = json_decode($request->getContent(), true) ?? [
            'request_type' => $request->query->get('request_type'),
            'start_at' => $request->query->get('start_at'),
            'end_at' => $request->query->get('end_at'),
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
        } else {
            $query = $repository->createQueryBuilder('r')->getQuery();
        }

        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        try {
            $pagerfanta->setCurrentPage($page);
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
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

    //PAGE DETAILS HISTORIQUE DES DEMANDES
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/detail-history-request/{id}', name: 'app_detail_history_request')]
    public function viewDetailRequestHistory(Security $security, int $id, RequestRepository $requestRepository, RequestFondation $request,): Response
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

        return $this->render('manager/detail_history_request.html.twig', [
            'page' => 'history-request',
            'request' => $requestLoaded,
            'form' => $form->createView(),
        ]);
    }
}
