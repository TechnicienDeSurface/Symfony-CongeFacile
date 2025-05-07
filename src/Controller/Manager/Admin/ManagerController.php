<?php

namespace App\Controller\Manager\Admin;

use App\Entity\User;
use App\Entity\Person;
use App\Form\ManagerType;
use App\Repository\UserRepository;
use App\Form\FilterManagerFormType;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartmentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ManagerController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    //PAGE MANAGER VIA ADMINISTRATION MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-manager/{page}', name: 'app_administration_manager', methods: ['GET', 'POST'])]
    public function viewManager(Request $request, UserRepository $UserRepository, int $page = 1): Response
    {
        $form = $this->createForm(FilterManagerFormType::class, null, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        $filters = [
            'last_name' => '',
            'first_name' => '',
            'department' => '',
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $filters = [
                'last_name' => $data['LastName'] ?? '',
                'first_name' => $data['FirstName'] ?? '',
                'department' => $data['Department'] ?? '',
            ];
        }

        $query = $UserRepository->findManagersWithFilters($filters);

        //PAGINATION
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);

        try {
            if ($form->isSubmitted()) {
                $page = 1;
            }
            $pagerfanta->setCurrentPage($page);
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }

        // Retourner les données dans la vue
        return $this->render('manager/admin/manager/manager.html.twig', [
            'page' => 'administration-manager',
            'persons' => $pagerfanta->getCurrentPageResults(),
            'filters' => $filters,
            'form' => $form->createView(),
            'pager' => $pagerfanta,
        ]);
    }

    //PAGE AJOUTER MANAGER VIA ADMINISTRATION MANAGER
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-ajouter-manager', name: 'app_administration_ajouter_manager')]
    public function addManager(UserPasswordHasherInterface $hash, ManagerRegistry $registry, Security $security, Request $request, PositionRepository $position_repository, PersonRepository $person_repository, DepartmentRepository $department_repository, UserRepository $user_repository): Response
    {
        $users = $user_repository->findBy([], []);
        $exist_email = false;
        $manager = new User();
        $manager = $security->getUser();
        if (!$manager instanceof \App\Entity\User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de App\Entity\User.');
        }
        $form = $this->createForm(ManagerType::class, null, ['submit_label' => 'Ajouter']);
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
                            $new_manager = new Person();
                            $new_manager->setFirstName($formData['first_name']);
                            $new_manager->setLastName($formData['last_name']);
                            $new_manager->setDepartment($formData['department']);
                            $new_manager->setAlertBeforeVacation(false);
                            $new_manager->setAlertNewRequest(false);
                            $new_manager->setAlertOnAnswer(true);
                            $new_manager->setPosition($formData['position']);
                            $new_manager->setManager(null);
                            $registry->getManager()->persist($new_manager);
                            $registry->getManager()->flush();
                            $this->addFlash('success', 'Succès pour ajouter le manager');
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Erreur pour l\'ajout du manager');
                        }
                        try {
                            $person = $registry->getManager()->getRepository(Person::class)->find($new_manager->getId());
                            $new_user = new User();
                            $new_user->setEmail($formData['email']);
                            $password_hash = $this->passwordHasher->hashPassword($new_user, $formData['newPassword']);
                            $new_user->setPassword($password_hash);
                            $new_user->setPerson($person);
                            $new_user->setRoles([1 => "ROLE_MANAGER"]);
                            $new_user->setIsVerified(true);
                            $new_user->setEnabled(true);
                            $registry->getManager()->persist($new_user);
                            $registry->getManager()->flush();
                            $this->addFlash('success', 'Succès pour ajouter de l\'utilisateur');
                            return $this->redirectToRoute('app_administration_manager');
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Erreur pour l\'ajout de l\'utilisateur');
                        }
                    } else {
                        $this->addFlash('error', 'Erreur l\'email existe déjà');
                    }
                } else {
                    $this->addFlash('error', 'Erreur la confirmation mot de passe n\'est pas identique au nouveau mot de passe');
                }
            }
        }

        return $this->render('manager/admin/manager/add_manager.html.twig', [
            'page' => 'administration-ajouter-manager',
            'manager' => $manager,
            'form' => $form,
            'submit_label' => 'Ajouter',
        ]);
    }

    #[IsGranted('ROLE_MANAGER')]
    #[Route('/administration-detail-manager/{id}', name: 'app_administration_detail_manager', methods: ['GET', 'POST'])]
    public function editManager(
        DepartmentRepository $departmentRepository,
        PersonRepository $personRepository,
        int $id,
        EntityManagerInterface $entityManager,
        Request $request,
        UserRepository $userRepository
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé pour l\'ID ' . $id);
        }

        $manager = $user->getPerson();

        if (!$manager) {
            throw $this->createNotFoundException('Aucune personne liée à cet utilisateur.');
        }

        $existEmail = false;

        $form = $this->createForm(ManagerType::class, [
            'email' => $user->getEmail(),
            'first_name' => $manager->getFirstName(),
            'last_name' => $manager->getLastName(),
            'department' => $manager->getDepartment(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $formData = $form->getData();

                // Vérification de l'email existant
                if (!empty($formData['email']) && $formData['email'] !== $user->getEmail()) {
                    $existingUser = $userRepository->findOneBy(['email' => $formData['email']]);
                    if ($existingUser) {
                        $existEmail = true;
                    }
                }

                if (!$existEmail) {
                    try {
                        // Mise à jour de la personne
                        $manager->setFirstName($formData['first_name']);
                        $manager->setLastName($formData['last_name']);
                        $manager->setDepartment(
                            $departmentRepository->find($formData['department'])
                        );
                        $entityManager->persist($manager);

                        // Mise à jour de l'utilisateur
                        $user->setEmail($formData['email']);

                        if (!empty($formData['newPassword']) && !empty($formData['confirmPassword'])) {
                            if ($formData['newPassword'] === $formData['confirmPassword']) {
                                $passwordHash = $this->passwordHasher->hashPassword($user, $formData['newPassword']);
                                $user->setPassword($passwordHash);
                            } else {
                                $this->addFlash('error', 'La confirmation du mot de passe ne correspond pas.');
                            }
                        }

                        $entityManager->persist($user);
                        $entityManager->flush();

                        $this->addFlash('success', 'Manager mis à jour avec succès.');
                        return $this->redirectToRoute('app_administration_manager');
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de la mise à jour.');
                    }
                } else {
                    $this->addFlash('error', 'L\'email existe déjà.');
                }
            }
        }

        return $this->render('manager/admin/manager/detail_manager.html.twig', [
            'page' => 'administration-manager',
            'form' => $form,
            'manager' => $manager,
            'user' => $user,
        ]);
    }
}
