<?php

namespace App\Controller\collaborator;

use App\Repository\RequestRepository;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request as RequestFondation;
use App\Form\RequestType ; 
use App\Entity\Request;
use App\Form\FilterRequestHistoryFormType;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use App\Entity\Person ; 
use App\Entity\User ; 
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RequestController extends AbstractController
{ 

    //PAGE NOUVELLE DEMANDE "COLLABORATEUR"
    #[IsGranted('ROLE_COLLABORATEUR')]
    #[Route('/new-request', name: 'app_new_request')]
    public function viewNewRequest(Security $security, RequestFondation $request_bd, RequestRepository $repository, ManagerRegistry $registry, PersonRepository $personRepository): Response
    {
        $request = new Request();
        
        // Récupérez l'utilisateur connecté
        $user = New User() ; 
        $user = $security->getUser() ;
        $persons = $personRepository->findBy([],[]);
        $person = New Person() ; 
        foreach ($persons as $row) {
            if($user == $row->getUser()){
                $person = $row ; 
            }
        }
        $request->setCollaborator($person);
        $request->setDepartment($person->getDepartment());
        // Créez le formulaire avec l'instance de Request
        $form = $this->createForm(RequestType::class, $request);

        $form->handleRequest($request_bd);

        if ($form->isSubmitted()) {
            if($form->isValid()){
                try {
                    // Si valide : j'enregistre les données dans la BDD.
                    $data = $form->getData();
                    $registry->getManager()->persist($data);
                    $registry->getManager()->flush();
                    $this->addFlash('success', 'La demande a été créée');
                    return $this->redirectToRoute('app_request_history_collaborator');
                } catch (NotFoundHttpException $e) {
                    $this->addFlash('error', 'Erreur lors de la création de la demande');
                }
            }else {
                $this->addFlash('error', 'Erreur lors de la validation de la demande');
            }
        }
        return $this->render('collaborator/new_request.html.twig', [
            'page' => 'new-request',
            'form' => $form,
        ]);
    }


    //PAGE HISTORIQUE DES DEMANDES "COLLABORATEUR"
    #[IsGranted('ROLE_COLLABORATEUR')]
    #[Route('/request-history-collaborator/{page}', name: 'app_request_history_collaborator', methods: ['GET', 'POST'])]
    public function viewRequestHistory(RequestFondation $request, RequestRepository $requestRepository, int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COLLABORATEUR');

        $form = $this->createForm(FilterRequestHistoryFormType::class);
        $form->handleRequest($request);

        $filters = [
            'request_type'     => $request->query->get('requesttype'),
            'created_at'     => $request->query->get('created_at'),
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

        return $this->render('collaborator/request_history.html.twig', [
            'page' => 'request-history-collaborator',
            'pager' => $pagerfanta,
            'form' => $form->createView(),
            'filters' => $filters,
            'request' => $pagerfanta->getCurrentPageResults(),
        ]);
    }

    //PAGE DETAILS DES DEMANDES "COLLABORATEUR"
    #[IsGranted('ROLE_COLLABORATEUR')]
    #[Route('/detail-request-collaborator/{id}', name: 'app_detail_request_collaborator')]
    public function detailRequest(): Response
    {
        return $this->render('collaborator/detail_request.html.twig', [
            'page' => 'detail-request-collaborator',
        ]);
    }

}
