<?php

namespace App\Controller\Collaborateur;

use App\Entity\Person;
use App\Entity\User;
use App\Form\InformationFormType;
use App\Form\MotDePasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InformationController extends AbstractController
{
    //PAGE INFORMATIONS "COLLABORATEUR"
    #[Route('/information-collaborateur', name: 'app_information_collaborateur')]
    public function viewInformationCollaborateur(): Response
    {
        $person = new Person();
        $user = new User() ;
        $form_person= $this->createForm(InformationFormType::class, $person);
        $form_password = $this->createForm(MotDePasseType::class, $user);

        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     // Handle form submission, e.g., save the data to the database
        // }

        return $this->render('collaborateur/information.html.twig', [
            'page' => 'information-collaborateur',
            'form1' => $form_person,
            'form2' => $form_password, 
        ]);
    }
}
