<?php

namespace App\Controller\Collaborator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreferenceController extends AbstractController
{
    //PAGE PREFERENCE "COLLABORATEUR"
    #[Route('/preference-collaborator', name: 'app_preference_collaborator')]
    public function viewPreferenceCollaborateur(): Response
    {
        return $this->render('collaborator/preference.html.twig', [
            'page' => 'preference-collaborator',
        ]);
    }
}
