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
use Doctrine\ORM\EntityManagerInterface;
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

    //PAGE DETAIL MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-detail-manager/{id}', name: 'app_administration_detail_manager', methods: ['GET','POST'])]
    public function editManager(PersonRepository $repository, int $id, EntityManagerInterface $entityManager,Request $request,UserRepository $user_repository): Response
    {
        $manager = $repository->find($id);
        $user = $manager->getUser();
        if(!$manager){
            throw $this->createNotFoundException('No manager found for id ' . $id);
        }
        $form = $this->createForm(ManagerType::class);
        $form->handleRequest($request);
        $users = $user_repository->findBy([],[]);
        foreach($users as $row){
            if($row->getPerson()->getId() === $id){
                $user = $row;
            }
        }
        if($form->isSubmitted())
        {
            if($form->isValid()){
                try{
                    $formData = $form->getData();
                    $manager->setFirstName($formData['first_name']);
                    $manager->setLastName($formData['last_name']);
                    $manager->setDepartment($formData['department']);
                    dd($manager);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($manager);
                    $entityManager->flush();
                    $this->addFlash('success', 'Succès pour la mise à jour le manager');
                }catch(\Exception $e){
                    $this->addFlash('error', 'Erreur pour la mise à jour de manager'); 
                }try{
                    $user->setEmail($formData['email']);
                    $password_hash = $this->passwordHasher->hashPassword($user, $formData['newPassword']) ;
                    $user->setPassword($password_hash);
                    $user->setRoles([1 => "ROLE_MANAGER"]);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Succès pour la mise à jour l\'utilisateur');
                }catch(\Exception $e ){
                    $this->addFlash('error', 'Erreur pour la mise à jour utilisateur'); 
                }
            }else{
                dd($form->getErrors());

            }
        }
        return $this->render('manager/admin/manager/detail_manager.html.twig', [
            'page' => 'administration-detail-manager',
            'form'=>$form, 
            'manager'=>$manager,
            'user'=>$user,
        ]);
    }

    // #[Route('/administration-detail-manager', name: 'app_administration_detail_manager')]
    // public function detailManager(): Response
    // {
    //     return $this->render('manager/admin/manager/detail_manager.html.twig', [
    //         'page' => 'administration-detail-manager',
    //         'user'=>'',
            
    //     ]);
    // }

    //SUPPRIMER MANAGER VIA L'ADMINISTRATION DU PORTAIL MANAGER
    #[Route('/administration-supprimer-manager/{id}', name: 'app_administration_supprimer_manager', methods: ['POST', 'GET'])]
    public function delete(Request $request, int $id, UserRepository $repository_user, PersonRepository $repository, ManagerRegistry $registry, UserRepository $user_repository): Response 
    {
        $managerRecup = $repository->find($id);
        $form = $this->createForm(FilterManagerFormType::class, null, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        $Managers = $repository_user->findAllManagers();
        
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
        $users = $user_repository->findBy([],[]);
        foreach($users as $row){
            if($row->getPerson()->getId() === $id){
                $user = $row;
            }
        }
        if (!$managerRecup) {
            throw $this->createNotFoundException('Pas de manager trouvé pour cet id : ' . $id);
        }

        $entityManager = $registry->getManager();
        // $productCount = $productRepository->countByCategory($manager->getId());
        try{
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }catch(\Exception $e){
            $this->addFlash('error', 'Erreur de suppression de l\'utilisateur');
        }
        try{
            $entityManager->remove($managerRecup);
            $entityManager->flush();
            $this->addFlash('success', 'Manager supprimé avec succès.');
        }catch(\Exception $e){
            $this->addFlash('error', 'Erreur de suppression du manager');
        }
        
        return $this->render('manager/admin/manager/manager.html.twig', [
            'page' => 'administration-manager',
            'manager'=>$manager,
            'form'=>$form,
            'persons'=>$persons, 
            'filters' => $filters,
        ]);
    }
}
