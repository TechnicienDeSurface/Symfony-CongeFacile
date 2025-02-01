<?php

namespace App\Controller\Collaborateur;

use App\Entity\Person;
use App\Form\InformationFormType;
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
        $form = $this->createForm(InformationFormType::class, $person);

        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     // Handle form submission, e.g., save the data to the database
        // }

        return $this->render('collaborateur/information.html.twig', [
            'page' => 'information-collaborateur',
            'form' => $form,
        ]);
    }
}
