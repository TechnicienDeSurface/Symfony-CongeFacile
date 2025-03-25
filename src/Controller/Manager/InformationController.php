<?php

namespace App\Controller\Manager;

use App\Entity\User;
use App\Form\InformationFormType;
use App\Form\MotDePasseType;
use Proxies\__CG__\App\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InformationController extends AbstractController
{
    //PAGE D'INFORMATIONS SUR LES DONNEES DU MANAGER
    #[Route('/information-manager', name: 'app_information_manager')]
    public function viewInformationManager(): Response
    {
        $person = new Person();
        $user = new User() ;
        $form_person= $this->createForm(InformationFormType::class, $person, [
            'is_manager' => true,
        ]);

        $form_password = $this->createForm(MotDePasseType::class, $user, [
            'is_manager' => true,
        ]);

        return $this->render('manager/information.html.twig', [
            'page' => 'information-manager',
            'formInfo' => $form_person->createView(),
            'formMdp' => $form_password->createView(),
        ]);
    }
}
