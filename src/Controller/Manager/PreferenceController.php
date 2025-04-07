<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\PreferenceManagerType;

class PreferenceController extends AbstractController
{
    //PAGE DES PREFERENCES POUR LE MANAGER
    #[Route('/preference-manager', name: 'app_preference_manager')]
    public function viewPreferenceManager(): Response
    {
        $form = $this->createForm(PreferenceManagerType::class);

        return $this->render('manager/preference.html.twig', [
            'page' => 'preference-manager',
            'form' => $form->createView(),
        ]);
    }
}
