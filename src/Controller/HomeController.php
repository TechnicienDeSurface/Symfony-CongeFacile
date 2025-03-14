<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function viewHome(): Response
    {
        return $this->render('home/home.html.twig', [
            'page' => 'accueil', //définir la page
            'bleu' => '#004C6C', //définit la couleur du fond de l'étape 1 en back  
            'blanc' => '#FFFFFF', 
        ]);
    }
}
