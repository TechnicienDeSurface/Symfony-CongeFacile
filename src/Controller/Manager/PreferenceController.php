<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\PreferenceManagerType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PreferenceController extends AbstractController
{
    //PAGE DES PREFERENCES POUR LE MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/preference-manager', name: 'app_preference_manager')]
    public function viewPreferenceManager(EntityManagerInterface $entityManager, Request $request, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        if (!$user instanceof User) {
            throw new \LogicException("Ce n'est pas un type user."); 
            // Dans certains cas, elle peut aussi retourner un autre type d'objet (comme une UserInterface générique).
            // Cette vérification évite d'accéder à la méthode getId() sur un objet qui ne serait pas un User (et donc éviter une erreur fatale).
        }

        // Récupérer l'entité Person associée
        $person = $user->getPerson();
        if (!$person) {
            throw $this->createNotFoundException('Aucune donnée personnelle trouvée pour cet utilisateur.');
        }

        // Créer le formulaire
        $form = $this->createForm(PreferenceManagerType::class, $person);
        $form->handleRequest($request);

        // Traiter le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($person);
            $entityManager->flush();

            $this->addFlash('success', 'Vos préférences ont été mises à jour.');
        }

        // Rendre la vue
        return $this->render('manager/preference.html.twig', [
            'form' => $form->createView(),
            'page' => 'preference-manager',
        ]);
    }
}
