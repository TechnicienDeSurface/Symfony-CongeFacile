<?php

namespace App\Controller\Manager;

use App\Entity\User;
use App\Entity\Person;
use App\Form\FilterManagerTeamFormType;
use App\Form\CollaborateurType;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TeamController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    //PAGE DE L'EQUIPE GERER PAR LE MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/team-manager/{page}', name: 'app_team', methods: ['GET', 'POST'])]
    public function viewTeam(Security $security, Request $request, PersonRepository $personRepository, int $page = 1): Response
    {
        $manager = new User();
        $manager = $security->getUser();
        if (!$manager instanceof User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de App\Entity\User.');
        }

        $form = $this->createForm(FilterManagerTeamFormType::class);
        $form->handleRequest($request);

        $filters = [
            'last_name'         => $request->query->get('lastname'),
            'first_name'        => $request->query->get('firstname'),
            'email'             => $request->query->get('email'),
            'name'              => $request->query->get('name'),
        ];

        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        $order = $filters['nbdays'] ?? '';

        // Recherche dans le repository avec les filtres
        $query = $personRepository->searchTeamMembers($filters, $manager, $order);

        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);

        try {
            $pagerfanta->setCurrentPage($page);
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }

        return $this->render('manager/team.html.twig', [
            'pager' => $pagerfanta,
            'team' => $pagerfanta->getCurrentPageResults(),
            'filters' => $filters,
            'form' => $form->createView(),
            'page' => 'team-manager',
        ]);
    }

    //PAGE DETAILS DE L'EQUIPE GERER PAR LE MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/detail-team-manager', name: 'app_detail_team', methods: ['POST'])]
    public function viewDetailTeam(UserRepository $repository, Request $request, DepartmentRepository $department_repository, PersonRepository $person_repository, EntityManagerInterface $entityManager, PositionRepository $position_repository, Request $requestHttp): Response
    {
        $exist_email = false;
        $form = $this->createForm(CollaborateurType::class, null, [
            'csrf_token_id' => 'submit', //Ajout du token csrf id car formulaire non lié à une entité 
            'submit_label' => 'Mettre à jour',
        ]);
        $form->handleRequest($request);

        $id = $requestHttp->request->get('id');
        $collaborator = $person_repository->find($id);
        $users = $repository->findBy([], []);

        foreach ($users as $row) {
            if ($row->getPerson() && $row->getPerson()->getId() === (int)$id) {
                $user = $row;
                break;
            }
        }
        
        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur associé au collaborateur n\'a été trouvé.');
        }
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $formData = $form->getData();
                if (!empty($formData['email']) && $formData['email'] != $user->getEmail()) {
                    foreach ($users as $row) {
                        if ($row->getEmail() == $formData['email']) {
                            $exist_email = true;
                        }
                    }
                }
                if ($exist_email == false) {
                    if (empty($formData['newPassword']) && empty($formData['confirmPassword'])) {
                        try {
                            if ($collaborator->getFirstName() != $formData['first_name']) {
                                $collaborator->setFirstName($formData['first_name']);
                            }
                            if ($collaborator->getLastName() != $formData['last_name']) {
                                $collaborator->setLastName($formData['last_name']);
                            }
                            if (!empty($formData['department'])) {
                                $department = $department_repository->find($formData['department']);
                                if ($department && $collaborator->getDepartment() != $department) {
                                    $collaborator->setDepartment($department);
                                }
                            }
                            if ($formData['position'] != $collaborator->getPositionId()) {
                                $position = $position_repository->find($formData['position']);
                                if ($position) {
                                    $collaborator->setPosition($position);
                                }
                            }
                            if ($collaborator->getUser()->isEnabled() != $formData['enabled']) {
                                $collaborator->getUser()->setEnabled((bool)$formData['enabled']);
                            }
                            $entityManager->persist($collaborator);
                            $entityManager->flush();
                            $this->addFlash('success', 'Succès pour la mise à jour du manager');
                        } catch (\Exception $e) {
                        }
                        try {
                            if ($user->getEmail() != $formData['email']) {
                                $user->setEmail($formData['email']);
                            }
                            $entityManager->persist($user);
                            $entityManager->flush();
                            $this->addFlash('success', 'Succès pour la mise à jour de l\'utilisateur');
                            return $this->redirectToRoute('app_team');
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Erreur pour la mise à jour de l\'utilisateur');
                        }
                    } else {
                        if ($formData['newPassword'] == $formData['confirmPassword']) {
                            try {
                                if ($collaborator->getFirstName() != $formData['first_name']) {
                                    $collaborator->setFirstName($formData['first_name']);
                                }
                                if ($collaborator->getLastName() != $formData['last_name']) {
                                    $collaborator->setLastName($formData['last_name']);
                                }
                                if (!empty($formData['department'])) {
                                    $department = $department_repository->find($formData['department']);
                                    if ($department && $collaborator->getDepartment() != $department) {
                                        $collaborator->setDepartment($department);
                                    }
                                }

                                if ($formData['position'] != $collaborator->getPositionId()) {
                                    $position = $position_repository->find($formData['position']);
                                    if ($position) {
                                        $collaborator->setPosition($position);
                                    }
                                }
                                if ($collaborator->getUser()->isEnabled() != $formData['enabled']) {
                                    $collaborator->getUser()->setEnabled((bool)$formData['enabled']);
                                }

                                $entityManager->persist($collaborator);
                                $entityManager->flush();
                                $this->addFlash('success', 'Succès pour la mise à jour le manager');
                            } catch (\Exception $e) {
                            }
                            try {
                                $password_hash = $this->passwordHasher->hashPassword($user, $formData['newPassword']);
                                if ($user->getEmail() != $formData['email']) {
                                    $user->setEmail($formData['email']);
                                }
                                if ($password_hash != $user->getPassword()) {
                                    $user->setPassword($password_hash);
                                }
                                $entityManager->persist($user);
                                $entityManager->flush();
                                $this->addFlash('success', 'Succès pour la mise à jour de l\'utilisateur');
                                return $this->redirectToRoute('app_team');
                            } catch (\Exception $e) {
                                $this->addFlash('error', 'Erreur pour la mise à jour de l\'utilisateur');
                            }
                        } else {
                            $this->addFlash('error', 'Erreur la confirmation mot de passe n\'est pas identique au nouveau mot de passe');
                        }
                    }
                } else {
                    $this->addFlash('error', 'Erreur l\'email existe déjà');
                }
            }
        } else {
            $form = $this->createForm(CollaborateurType::class, [
                'email' => $user->getEmail(),
                'first_name' => $collaborator->getFirstName(),
                'last_name' => $collaborator->getLastName(),
                'department' => $collaborator->getDepartment(),
                'position' => $collaborator->getPositionId(),
                'enabled' => $user->isEnabled(),
                'submit_label' => 'Mettre à jour',
            ]);
        }
        return $this->render('manager/detail_team.html.twig', [
            'controller_name' => 'Team',
            'user' => $user,
            'collaborator' => $collaborator,
            'page' => 'team-manager',
            'form' => $form,
        ]);
    }

    //PAGE AJOUTER MANAGER VIA ADMINISTRATION MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-ajouter-collaborateur', name: 'app_administration_ajouter_collaborateur')]
    public function addCollaborateur(UserPasswordHasherInterface $hash, ManagerRegistry $registry, Security $security, Request $request, PositionRepository $position_repository, PersonRepository $person_repository, DepartmentRepository $department_repository, UserRepository $user_repository): Response
    {
        $users = $user_repository->findBy([], []);
        $exist_email = false;
        $manager = new User();
        $manager = $security->getUser();

        if (!$manager instanceof User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de App\Entity\User.');
        }

        $form = $this->createForm(CollaborateurType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $formData = $form->getData();
                if (!empty($formData['email'])) {
                    foreach ($users as $row) {
                        if ($row->getEmail() == $formData['email']) {
                            $exist_email = true;
                        }
                    }
                }
                if ($formData['newPassword'] == $formData['confirmPassword']) {
                    if ($exist_email === false) {
                        try {
                            $new_collaborator = new Person();
                            $new_collaborator->setFirstName($formData['first_name']);
                            $new_collaborator->setLastName($formData['last_name']);
                            $new_collaborator->setDepartment($formData['department']);
                            $new_collaborator->setAlertBeforeVacation(false);
                            $new_collaborator->setAlertNewRequest(false);
                            $new_collaborator->setAlertOnAnswer(true);
                            $new_collaborator->setPosition($formData['position']);
                            $new_collaborator->setManager($manager);
                            $registry->getManager()->persist($new_collaborator);
                            $registry->getManager()->flush();
                            $this->addFlash('success', 'Succès pour ajouter le collaborateur');
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Erreur pour l\'ajout de collaborateur');
                        }
                        try {
                            $person = $registry->getManager()->getRepository(Person::class)->find($new_collaborator->getId());
                            $new_user = new User();
                            $new_user->setEmail($formData['email']);
                            $password_hash = $this->passwordHasher->hashPassword($new_user, $formData['newPassword']);
                            $new_user->setPassword($password_hash);
                            $new_user->setPerson($person);
                            $new_user->setRoles(["ROLE_COLLABORATEUR"]);
                            $new_user->setIsVerified(true);
                            $new_user->setEnabled(true);
                            $registry->getManager()->persist($new_user);
                            $registry->getManager()->flush();
                            $this->addFlash('success', 'Succès pour ajouter l\'utilisateur');
                            return $this->redirectToRoute('app_team');
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Erreur pour l\'ajout utilisateur');
                        }
                    } else {
                        $this->addFlash('error', 'Erreur l\'email existe déjà');
                    }
                } else {
                    $this->addFlash('error', 'Erreur la confirmation mot de passe n\'est pas identique au nouveau mot de passe');
                }
            }
        }

        return $this->render('manager/add_collaborator.html.twig', [
            'page' => 'administration-ajouter-collaborateur',
            'manager' => $manager,
            'form' => $form,
        ]);
    }

    //PAGE SUPPRIMER UN COLLABORATEUR
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/admin-delete-collaborator/{id}', name: 'app_admin_delete_collaborator', methods: ['GET'])]
    public function deleteCollaborateur(int $id, PersonRepository $person_repository, EntityManagerInterface $entityManager): Response
    {
        $collaborator = $person_repository->find($id);
        $userCollaborator = $collaborator->getUser();
        $requests = $collaborator->getRequests();
        if (!$collaborator) {
            throw $this->createNotFoundException('Pas de collaborateur avec cet ID : ' . $id);
        }
        try {
            $entityManager->remove($userCollaborator);
            foreach ($requests as $request) {
                $entityManager->remove($request);
            }
            $entityManager->remove($collaborator);
            $entityManager->flush();
            $this->addFlash('success', 'Collaborateur supprimé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression du collaborateur.');
        }

        return $this->redirectToRoute('app_team');
    }
}
