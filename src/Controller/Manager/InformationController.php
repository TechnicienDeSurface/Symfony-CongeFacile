<?php 

namespace App\Controller\Manager;

use App\Entity\User;
use App\Entity\Person;
use App\Form\InformationFormType;
use App\Form\MotDePasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
    
class InformationController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/information-manager', name: 'app_information_manager')]
    public function viewInformationManager(Security $security,UserPasswordHasherInterface $hash,EntityManagerInterface $entityManager, Request $request): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        if (!$user instanceof User) {
            throw new \LogicException("Ce n'est pas un type user."); 
            // Dans certains cas, elle peut aussi retourner un autre type d'objet (comme une UserInterface générique).
            // Cette vérification évite d'accéder à la méthode getId() sur un objet qui ne serait pas un User (et donc éviter une erreur fatale).
        }
        
        // Accéder à la Person directement depuis l'objet User
        $person = $user->getPerson();

        if (!$person) {
            throw $this->createNotFoundException('Aucune information trouvée pour cet utilisateur.');
        }

        // Création des formulaires
        $form_person = $this->createForm(InformationFormType::class, $person, [
            'is_manager' => true,
            'data_class' => Person::class, // Ensure the form expects a Person object
            'user' => $user,
        ]);
        
        $form_password = $this->createForm(MotDePasseType::class, $user, [
            'is_manager' => true,
            'data_class' => User::class, // Ensure the form expects a User object
        ]);

        $form_password->handleRequest($request);
        if ($form_password->isSubmitted() && $form_password->isValid()) {
            $currentPassword = $form_password->get('currentPassword')->getData();
            $newPassword = $form_password->get('newPassword')->getData();
            $confirmPassword = $form_password->get('confirmPassword')->getData();

            if ($hash->isPasswordValid($user, $currentPassword)) {
            if ($newPassword === $confirmPassword) {
                if ($newPassword !== $currentPassword) {
                $password = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($password);
                $entityManager->flush();
                $this->addFlash('success', 'Mot de passe modifié avec succès.');
                } else {
                $this->addFlash('error_repeat', 'Le nouveau mot de passe ne peut pas être identique à l\'ancien.');
                }
            } else {
                $this->addFlash('error_comfirm', 'La confirmation du mot de passe ne correspond pas.');
            }
            } else {
            $this->addFlash('error_current', 'Le mot de passe actuel est incorrect.');
            }
        }

        // Rendu du template avec les formulaires
        return $this->render('manager/information.html.twig', [
            'page' => 'information-manager',
            'formInfo' => $form_person->createView(),
            'formMdp' => $form_password->createView(),
        ]);
    }
}
