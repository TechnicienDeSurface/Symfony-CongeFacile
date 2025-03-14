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

    #[Route('/home', name: 'home')]
    public function accueil(): Response
    {
        // $paragraphe = "CongéFacile est votre nouvel outil dédié à la gestion des congés au sein de l'entreprise.<br>
        // Plus besoin d'échanges interminables ou de formulaires papier : en quelques clics, vous pouvez gérer<br>
        // vos absences en toute transparence et simplicité." ; 
        return $this->render('accueil/accueil.html.twig', [
            'page' => 'accueil', //définir la page
            'etape1_fond' => '#004C6C', //définit la couleur du fond de l'étape 1 en back  
            'etape1_texte' => '#FFFFFF', //définit la couleur du texte et du bord du cercle de l'étape 1 en back 
        ]); 
    }

    #[Route('/motdepasse', name: 'motdepasse')]
    public function motdepasse(): Response
    {
        
        $paragraphe = "" ; 
        return $this->render('authentification/form_mdp.html.twig', [
            'page' => 'motdepasse', //définir la page
            'titre'=>'Mot de passe oublié',  //définir le titre 
            'paragraphe' => $paragraphe, 
        ]); 
    }

    #[Route('/accueil', name:'accueil')]
    public function manager(): Response
    {
        return $this->render('manager/index.html.twig', [
            'nombre' => 2 ,
        ]) ; 
    }


}
