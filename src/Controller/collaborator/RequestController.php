<?php

namespace App\Controller\Collaborator;

use App\Repository\RequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request as RequestFondation;
use App\Entity\Request ; 
use App\Form\RequestType ; 

class RequestController extends AbstractController
{
    //PAGE NOUVELLE DEMANDE "COLLABORATEUR"
    #[Route('/new-request', name: 'app_new_request')]
    public function viewNewRequest(RequestFondation $request_bd, RequestRepository $repository, ManagerRegistry $registry): Response
    {
        $request = new Request() ; 
        $form = $this->createForm(RequestType::class,$request); 
        $form->handleRequest($request_bd); 
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    // Si valide : j'enregistre les données dans la BDD.
                    $registry->getManager()->persist($request);
                    $registry->getManager()->flush();
                    $this->addFlash('success', 'Success to add product') ; 
                    return $this->render('accueil/accueil.html.twig', [
                        'page' => 'accueil', //définir la page
                    ]);  
                } catch (NotFoundHttpException $e ){
                    $this->addFlash('error', 'Error to add product') ; 
                }
                // Faire une redirection vers le formulaire de modification.
            } else {
                // Sinon j'affiche un message d'erreur
                $this->addFlash('error', 'Error to add product') ; 
            }
        }else{
            
        }

        return $this->render('collaborator/new_request.html.twig', [
            'page' => 'new-request',
            'form' => $form->createView(),
        ]);
    }

    //PAGE HISTORIQUE DES DEMANDES "COLLABORATEUR"
    #[Route('/request-history', name: 'app_request_history')]
    public function viewRequestHistory(): Response
    {
        return $this->render('collaborator/request_history.html.twig', [
            'page' => 'request-history',
        ]);
    }

    //PAGE DETAILS DES DEMANDES "COLLABORATEUR"
    #[Route('/detail-request-collaborator/{id}', name: 'app_detail_request_collaborator')]
    public function detailRequest(): Response
    {
        return $this->render('collaborator/detail_request.html.twig', [
            'page' => 'detail-request-collaborator',
        ]);
    }

}
