<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function viewHome(): Response
    {
        return $this->render('default/index.html.twig', [
            'page' => 'home',
        ]);
    }

    #[Route('/accueil', name: 'accueil')]
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
            'page' => 'accueil',
            'titre'=>'CongéFacile', 
            'paragraphe' => $paragraphe, 
            'titre2' => 'Etapes', 
            'texte1' =>$texte1, 
            'texte2 '=>$texte2,
            'texte3' =>$texte3,  
        ]); 
    }
}
