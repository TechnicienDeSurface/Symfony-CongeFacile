<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // #[Route('/home', name: 'home')]
    // public function viewHome(): Response
    // {
    //     return $this->render('default/index.html.twig', [
    //         'page' => 'home',
    //     ]);
    // }

    #[Route('/home', name: 'app_home')]
    public function accueil(): Response
    {
        // $paragraphe = "CongéFacile est votre nouvel outil dédié à la gestion des congés au sein de l'entreprise.<br>
        // Plus besoin d'échanges interminables ou de formulaires papier : en quelques clics, vous pouvez gérer<br>
        // vos absences en toute transparence et simplicité." ; 
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
    }

    #[Route('/motdepasse', name: 'motdepasse')]
    public function motdepasse(): Response
    {
        
        $paragraphe = "Renseignez votre adresse email dans le champ ci-dessous.
Vous recevrez par la suite un email avec un lien vous permettant de réinitialiser votre mot de passe." ; 
        return $this->render('authentification/form_mdp.html.twig', [
            'page' => 'motdepasse', //définir la page
            'titre'=>'Mot de passe oublié',  //définir le titre 
            'paragraphe' => $paragraphe, 
        ]); 
    }


}
