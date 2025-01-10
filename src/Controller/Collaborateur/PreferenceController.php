<?php

namespace App\Controller\Collaborateur;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreferenceController extends AbstractController
{
    //PAGE PREFERENCE "COLLABORATEUR"
    #[Route('/preference-collaborateur', name: 'app_preference_collaborateur')]
    public function viewPreferenceCollaborateur(): Response
    {
        return $this->render('collaborateur/preference.html.twig', [
            'page' => 'preference-collaborateur',
        ]);
    }
}
