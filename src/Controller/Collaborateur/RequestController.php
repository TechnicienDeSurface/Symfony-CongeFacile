<?php

namespace App\Controller\Collaborateur;

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
                try{
                    // Si valide : j'enregistre les données dans la BDD.
                    $registry->getManager()->persist($request);
                    $registry->getManager()->flush();
                    $this->addFlash('success', 'Success to add product') ; 
                    $paragraphe = "CongéFacile est votre nouvel outil dédié à la gestion des congés au sein de l'entreprise.
                    Plus besoin d'échanges interminables ou de formulaires papier : en quelques clics, vous pouvez gérer
                    vos absences en toute transparence et simplicité." ; 
                    $texte1 = "j'effectue ma demande de congés"; 
                    $texte2 = "Mon manager valide ou refuse la demande" ; 
                    $texte3 = "Je consulte l'historique de mes demandes"; 
                    return $this->render('accueil/accueil.html.twig', [
                        'page' => 'accueil', //définir la page
                        'titre'=>'CongéFacile',  //définir le titre 
                        'paragraphe' => $paragraphe, 
                        'titre2' => 'Etapes', 
                        'texte1' =>$texte1, 
                        'texte2 '=>$texte2,
                        'texte3' =>$texte3,  
                        'etape1_fond' => '#004C6C', //définit la couleur du fond de l'étape 1 en back  
                        'etape1_texte' => '#FFFFFF', //définit la couleur du texte et du bord du cercle de l'étape 1 en back 
                        'etape2_fond' => '#004C6C', 
                        'etape2_texte' => '#FFFFFF',
                        'etape3_fond' => '#FFFFFF', 
                        'etape3_texte' => '#004C6C',
                    ]);  
                }catch(NotFoundHttpException $e ){
                    $this->addFlash('error', 'Error to add product') ; 
                }
                // Faire une redirection vers le formulaire de modification.
            } else {
                // Sinon j'affiche un message d'erreur
                $this->addFlash('error', 'Error to add product') ; 
            }
        }else{
            
        }

        return $this->render('collaborateur/new_request.html.twig', [
            'page' => 'new-request',
            'form' => $form->createView(),
        ]);
    }

    //PAGE HISTORIQUE DES DEMANDES "COLLABORATEUR"
    #[Route('/request-history', name: 'app_request_history')]
    public function viewRequestHistory(): Response
    {
        return $this->render('collaborateur/request_history.html.twig', [
            'page' => 'request-history',
        ]);
    }

    //PAGE DETAILS DES DEMANDES "COLLABORATEUR"
    #[Route('/detail-request-collaborateur/{id}', name: 'app_detail_request_collaborateur')]
    public function detailRequest(): Response
    {
        return $this->render('collaborateur/detail_request.html.twig', [
            'page' => 'detail-request-collaborateur',
        ]);
    }

}
