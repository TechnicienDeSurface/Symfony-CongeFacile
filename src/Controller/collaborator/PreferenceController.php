<?php

namespace App\Controller\collaborator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PersonRepository; 
use App\Form\PreferenceType; 
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\MailerService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PreferenceController extends AbstractController
{
    //PAGE PREFERENCE "COLLABORATEUR"
    #[IsGranted('ROLE_COLLABORATEUR')]
    #[Route('/preference-collaborateur', name: 'app_preference_collaborateur')]
    public function viewPreferenceCollaborateur(ManagerRegistry $registry, PersonRepository $repository, Request $request, MailerService $mailer ): Response
    {
        /** @var User $user */ 
        $user = $this->getUser() ; //récupère l'objet de l'utilisateur connecté  
        $userHost = $user->getPerson() ; 
        $id = $userHost->getId();
        
        $person = $repository->find($id) ; 
        if(!$person){
            throw $this->createNotFoundException('No person found for id ' . $id);
        }
        $form = $this->createForm(PreferenceType::class, $person ) ; 
        $form->handleRequest($request) ; 

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Si valide : j'enregistre les données dans la BDD.
                $request->cookies->all();
                $registry->getManager()->flush();
                $this->addFlash('success','Modifications de préférences enregistées');
                // Faire une redirection vers le formulaire de modification.
            } else {
                // Sinon j'affiche un message d'erreur
                $this->addFlash('error','Erreur lors des modifications de préférences enregistées');
            }
        }

        return $this->render('collaborator/preference.html.twig', [
            'page' => 'preference-collaborateur',
            'form' => $form , 
        ]);
    }
}
