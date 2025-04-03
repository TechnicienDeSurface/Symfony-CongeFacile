<?php

namespace App\Controller\Collaborator;

use App\Entity\Person;
use App\Entity\User;
use App\Form\MotDePasseType;
use App\Form\InformationFormType;
use App\Repository\UserRepository ; 
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InformationController extends AbstractController

{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){
        
    }
    //PAGE INFORMATIONS "COLLABORATEUR"
    #[Route('/information-collaborateur', name: 'app_information_collaborateur')]
    public function viewInformationCollaborateur(ManagerRegistry $registry, Security $security, Request $request,UserPasswordHasherInterface $hash,EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $user = new User() ;
        $collaborator = New User() ; 
        $collaborator = $security->getUser() ;
        $form_person= $this->createForm(InformationFormType::class, $person);
        $form_password = $this->createForm(MotDePasseType::class, $user);
        $form_password->handleRequest($request);
        
        if ($form_password->isSubmitted()){
            if($form_password->isValid()) {
                $data = $form_password->getData() ; 
                $currentPassword = $data->currentPassword ; 
                $verifHash = $this->passwordHasher->hashPassword($user, $currentPassword) ;
                $newPassword = $data->newPassword ; 
                $confirmPassword = $data->confirmPassword ; 
                if($verifHash == $collaborator->password){
                    if($newPassword == $confirmPassword){
                        if($data->newPassword || $data->confirmPassword != $currentPassword){
                            $password = $this->passwordHasher->hashPassword($user, $newPassword) ; 
                            $user->setPassword($password) ; 
                            $registry->getManager()->flush(); // Mettre à jour l'entité
                            $this->addFlash('success', 'Mot de passe modifié');
                        }else{
                            $this->addFlash('error', 'Le nouveau mot passe est le même que l\'actuel');
                        }
                    }else{
                        $this->addFlash('error', 'La confirmation du nouveau mot de passe ne correspond pas au nouveau mot de passe');
                    }   
                }else{
                    $this->addFlash('error', 'Le mot de passe actuel est incorrect');   
                }
            }
        }

        return $this->render('collaborator/information.html.twig', [
            'page' => 'information-collaborator',
            'form1' => $form_person,
            'form2' => $form_password, 
        ]);
    }
}
