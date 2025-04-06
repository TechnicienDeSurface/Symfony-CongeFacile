<?php

namespace App\Controller\Manager\Admin;

use App\Entity\User; 
use App\Entity\Person; 
use App\Entity\Position;
use App\Form\ManagerType;
use App\Entity\Department;  
use App\Repository\UserRepository; 
use App\Form\FilterManagerFormType;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository; 
use App\Repository\DepartmentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ManagerController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){
        
    }

    //PAGE MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-manager', name: 'app_administration_manager', methods: ['GET', 'POST'])]
    public function viewManager(Request $request, PersonRepository $repository, UserRepository $UserRepository): Response
    {
        $form = $this->createForm(FilterManagerFormType::class, null, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        $Managers = $UserRepository->findAllManagers();
        
        $personIds = [];
        $userIds = [];

        foreach ($Managers as $manager) {
            $personIds[] = $manager['person_id'];
            $userIds[] = $manager['user_id'];
        }

        $persons = [];
        foreach ($personIds as $personId) {
            $person = $repository->find($personId);
            if ($person) {
                $persons[] = $person;
            }
        }

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

            $persons = $repository->findByFilters($filters);
        }

        return $this->render('manager/admin/manager/manager.html.twig', [
            'page' => 'administration-manager',
            'managers' => $Managers,
            'persons' => $persons,
            'filters' => $filters,
            'form' => $form->createView(),
        ]);
    }

    //PAGE AJOUTER MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-manager', name: 'app_administration_ajouter_manager')]
    public function addManager(UserPasswordHasherInterface $hash, ManagerRegistry $registry, Security $security,Request $request, PositionRepository $position_repository, PersonRepository $person_repository, DepartmentRepository $department_repository, UserRepository $user_repository): Response
    {
        $manager = New User() ; 
        $manager = $security->getUser() ;
        if (!$manager instanceof \App\Entity\User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de App\Entity\User.');
        }
        $form = $this->createForm(ManagerType::class) ; 
        $form->handleRequest($request) ; 
        
        if($form->isSubmitted())
        {
            if($form->isValid()){
                try{
                    $formData = $form->getData();
                    $new_manager= new Person();
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
                }catch(\Exception $e){
                    $this->addFlash('error', 'Erreur pour l\'ajout de manager'); 
                }try{
                    $person = $registry->getManager()->getRepository(Person::class)->find($new_manager->getId());
                    $new_user = New User();
                    $new_user->setEmail($formData['email']);
                    $password_hash = $this->passwordHasher->hashPassword($new_user, $formData['newPassword']) ;
                    $new_user->setPassword($password_hash);
                    $new_user->setPerson($person);
                    $new_user->setRoles([1 => "ROLE_MANAGER"]);
                    $new_user->setIsVerified(true);
                    $new_user->setEnabled(true);
                    $registry->getManager()->persist($new_user);
                    $registry->getManager()->flush();
                    $this->addFlash('success', 'Succès pour ajouter l\'utilisateur');
                }catch(\Exception $e ){
                    $this->addFlash('error', 'Erreur pour l\'ajout utilisateur'); 
                }
            }
        }
        
        return $this->render('manager/admin/manager/add_manager.html.twig', [
            'page' => 'administration-ajouter-manager',
            'manager'=>$manager,
            'form'=>$form,
        ]);
    }

    // //PAGE DETAIL MANAGER VIA ADMINISTRATION MANAGER
    // #[Route('/administration-detail-manager/{id}', name: 'app_administration_detail_manager')]
    // public function editManager(PersonRepository $repository, int $id, ManagerRegistry $registry,Request $request): Response
    // {
    //     $manager = $repository->find($id);
    //     if(!$manager){
    //         throw $this->createNotFoundException('No category found for id ' . $id);
    //     }
    //     $form = $this->createForm(UserType::class, $manager);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted()) {
    //         if ($form->isValid()) {
    //             $request->cookies->all();
    //             // Si valide : j'enregistre les données dans la BDD.
    //             $registry->getManager()->flush();
    //         } else {
    //             // Sinon j'affiche un message d'erreur
    //         }
    //     }
    //     return $this->render('manager/admin/manager/detail_manager.html.twig', [
    //         'page' => 'administration-detail-manager',
    //     ]);
    // }

    #[Route('/administration-detail-manager', name: 'app_administration_detail_manager')]
    public function detailManager(): Response
    {
        return $this->render('manager/admin/manager/detail_manager.html.twig', [
            'page' => 'administration-detail-manager',
        ]);
    }

    //SUPPRIMER MANAGER VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-supprimer-manager/{id}', name: 'app_administration_supprimer_manager', methods: ['POST', 'DELETE'])]
    public function delete(int $id, UserRepository $productRepository, UserRepository $repository, ManagerRegistry $registry): Response 
    {
        $manager = $repository->find($id);

        if (!$manager) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        $entityManager = $registry->getManager();
        $productCount = $productRepository->countByCategory($manager->getId());

        if ($productCount > 0) {
            $this->addFlash('error', 'Impossible de supprimer ce manager car elle est utilisée par des produits.');
            return $this->redirectToRoute('category/list');
        } else {
            $entityManager->remove($manager);
            $entityManager->flush();
        }
        
        return $this->render('manager/admin/manager/manager.html.twig', [
            'page' => 'administration-manager',
        ]);
    }
}
