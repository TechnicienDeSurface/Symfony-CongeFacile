<?php

namespace App\Controller\collaborator;

use App\Entity\Person;
use App\Entity\User;
use App\Form\MotDePasseType;
use App\Form\InformationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class InformationController extends AbstractController

{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){
        
    }
    //PAGE INFORMATIONS "COLLABORATEUR"
    #[IsGranted('ROLE_COLLABORATEUR')]
    #[Route('/information-collaborateur', name: 'app_information_collaborateur')]
    public function viewInformationCollaborateur(ManagerRegistry $registry, Security $security, Request $request,UserPasswordHasherInterface $hash,EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $user = new User() ;
        $collaborator = New User() ; 
        $collaborator = $security->getUser() ;
        if (!$collaborator instanceof \App\Entity\User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de App\Entity\User.');
        }
        $person  = $collaborator->getPerson();
        $form_person= $this->createForm(InformationFormType::class, $person);
        $form_password = $this->createForm(MotDePasseType::class, $user);
        $form_password->handleRequest($request);
        
        if ($form_password->isSubmitted()){
            if($form_password->isValid()) {
                $data = $form_password->getData() ; 
                // $currentPassword = $data['currentPassword'] ;                 
                // $newPassword = $data['newPassword'] ; 
                // $confirmPassword = $data['confirmPassword'] ; 
                $currentPassword = $form_password->get('currentPassword')->getData();
                $newPassword = $form_password->get('newPassword')->getData();
                $confirmPassword = $form_password->get('confirmPassword')->getData();

                $verifHash = $this->passwordHasher->hashPassword($user, $currentPassword) ;
                if ($hash->isPasswordValid($collaborator, $currentPassword)) {
                    if($newPassword == $confirmPassword){
                        if($newPassword || $confirmPassword != $currentPassword){
                            $password = $this->passwordHasher->hashPassword($user, $newPassword) ; 
                            $collaborator->setPassword($password) ; 
                            $entityManager->flush(); // Mettre à jour l'entité

                            $this->addFlash('success', 'Mot de passe modifié'); //Création d'un message flash de succès
                        }else{
                            $this->addFlash('error', 'Le nouveau mot passe est le même que l\'actuel');
                        }
                    }else{
                        $this->addFlash('error', 'La confirmation du nouveau mot de passe ne correspond pas au nouveau mot de passe');
                    }   
                }else{
                    $this->addFlash('error', 'Le mot de passe actuel est incorrect'); //Création d'un message flash erreur 
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
