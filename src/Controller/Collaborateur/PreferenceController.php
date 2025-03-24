<?php

namespace App\Controller\Collaborateur;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PersonRepository ; 
use App\Form\PreferenceType ; 
use App\Entity\Person ;
use App\Entity\User ;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\MailerService ;
use PDOException;

class PreferenceController extends AbstractController
{
    //PAGE PREFERENCE "COLLABORATEUR"
    #[Route('/preference-collaborateur', name: 'app_preference_collaborateur')]
    public function viewPreferenceCollaborateur(ManagerRegistry $registry, PersonRepository $repository, Request $request, MailerService $mailer ): Response
    {
        try{
            $mailer->sendEmail('test.valdoise@gmail.com', 'Test Envoie email ', 'Test réussi', '<h1>Bienvenue</h1><p>Merci de vous être inscrit à notre service.</p><p>Cordialement,<br>L\'équipe</p>') ; 
        }catch(PDOException $e){
            dd($e) ; 
        }
        $id = null ; 
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
                //dd($request->cookies->all());
                // Si valide : j'enregistre les données dans la BDD.
                $request->cookies->all();
                try{
                    $mailer->sendEmail('test.valdoise@gmail.com', 'Test Envoie email ', 'Test réussi', '<h1>Bienvenue</h1><p>Merci de vous être inscrit à notre service.</p><p>Cordialement,<br>L\'équipe</p>') ; 
                }catch(PDOException $e){
                    dd($e) ; 
                }
                    $registry->getManager()->flush();

                // Faire une redirection vers le formulaire de modification.
            } else {
                // Sinon j'affiche un message d'erreur
                dd('Formulaire invalide');
            }
        }

        return $this->render('collaborateur/preference.html.twig', [
            'page' => 'preference-collaborateur',
            'form' => $form , 
        ]);
    }
}
