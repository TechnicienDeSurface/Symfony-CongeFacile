<?php

namespace App\Collaborateur\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreferenceController extends AbstractController
{
    //PREFERENCE "COLLABORATEUR"
    #[Route('/preference-collaborateur', name: 'app_preference_collaborateur')]
    public function index(): Response
    {
        return $this->render('collaborateur/preference.html.twig', [
            'page' => 'preference-collaborateur',
        ]);
    }
}
