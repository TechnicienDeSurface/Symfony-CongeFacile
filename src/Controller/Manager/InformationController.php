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

class InformationController extends AbstractController
{
    #[Route('/information-manager', name: 'app_information_manager')]
    public function viewInformationManager(Security $security): Response
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
            'person' => $person,
        ]);
        
        $form_password = $this->createForm(MotDePasseType::class, $user, [
            'is_manager' => true,
            'data_class' => User::class, // Ensure the form expects a User object
        ]);

        // Rendu du template avec les formulaires
        return $this->render('manager/information.html.twig', [
            'page' => 'information-manager',
            'formInfo' => $form_person->createView(),
            'formMdp' => $form_password->createView(),
        ]);
    }
}
