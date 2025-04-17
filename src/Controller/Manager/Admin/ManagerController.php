<?php

namespace App\Controller\Manager\Admin;

use App\Entity\User; 
use App\Entity\Person; 
use App\Entity\Position;
use App\Form\ManagerType;
use App\Form\DeleteType;
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
        $form = $this->createForm(ManagerType::class); 
        $form->handleRequest($request); 
        if($form->isSubmitted())
        {
            if($form->isValid()){
                $formData = $form->getData();
                if($formData['newPassword'] == $formData['confirmPassword']){
                    try{
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
        }
        
        return $this->render('manager/admin/manager/add_manager.html.twig', [
            'page' => 'administration-ajouter-manager',
            'manager'=>$manager,
            'form'=>$form,
        ]);
    }

    //PAGE DETAIL MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-detail-manager/{id}', name: 'app_administration_detail_manager', methods: ['GET','POST'])]
    public function editManager(DepartmentRepository $department_repository, PersonRepository $repository, int $id, EntityManagerInterface $entityManager,Request $request,UserRepository $user_repository): Response
    {
        $manager = $repository->find($id);
        $user = $manager->getUser();
        if(!$manager){
            throw $this->createNotFoundException('No manager found for id ' . $id);
        }
        $form = $this->createForm(ManagerType::class, null, [
            'csrf_token_id' => 'submit', //Ajout du token csrf id car formulaire non lié à une entité 
        ]);
        $form->handleRequest($request);
        $users = $user_repository->findBy([],[]);
        foreach($users as $row){
            if($row->getPerson()->getId() === $id){
                $user = $row;
            }
        }
        if($form->isSubmitted()){
            if($form->isValid()){
                $formData = $form->getData();
                if($formData['newPassword'] == $formData['confirmPassword']){
                    try{
                        if($manager->getFirstName() != $formData['first_name']){
                            $manager->setFirstName($formData['first_name']);
                        }
                        if($manager->getLastName() != $formData['last_name']){
                            $manager->setLastName($formData['last_name']);
                        }
                        if($manager->getDepartment != $formData['department']){
                            $department = $department_repository->find($formData['department']);
                            $manager->setDepartment($department);
                        }
                        $entityManager->persist($manager);
                        $entityManager->flush();
                        $this->addFlash('success', 'Succès pour la mise à jour le manager');
                    }catch(\Exception $e){

                    }try{
                        $password_hash = $this->passwordHasher->hashPassword($user, $formData['newPassword']) ;
                        if($user->getEmail() != $formData['email']){
                            $user->setEmail($formData['email']);
                        }
                        if($password_hash != $user->getPassword()){
                            $user->setPassword($password_hash);
                        }  
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->addFlash('success', 'Succès pour la mise à jour l\'utilisateur');
                    }catch(\Exception $e ){
                        $this->addFlash('error', 'Erreur pour la mise à jour utilisateur'); 
                    }
                }else{
                    $this->addFlash('error','Confirmation mot de passe incorrect');
                }
            }
        }else{
            $form = $this->createForm(ManagerType::class, [
                'email' => $user->getEmail(),
                'first_name' => $manager->getFirstName(),
                'last_name' => $manager->getLastName(),
                'department' => $manager->getDepartment(),
            ]);
        }
        return $this->render('manager/admin/manager/detail_manager.html.twig', [
            'page' => 'administration-detail-manager',
            'form'=>$form, 
            'manager'=>$manager,
            'user'=>$user,
        ]);
    }

    #[Route('/administration-supprimer-manager/{id}', name: 'app_administration_supprimer_manager', methods: ['POST', 'GET'])]
    public function deleteManager(PersonRepository $repository, Request $request, int $id, ManagerRegistry $registry,EntityManagerInterface $entityManager): Response
    {
        $manager = $repository->find($id);
        $user = $manager->getUser();
        $confirmation = $this->createForm(DeleteType::class, null, [
            'csrf_token_id' => 'submit', //Ajout du token csrf id car formulaire non lié à une entité 
        ]);
        $confirmation->handleRequest($request);

        $form = $this->createForm(ManagerType::class, null, [
            'csrf_token_id' => 'submit', //Ajout du token csrf id car formulaire non lié à une entité 
        ]);
        $form->handleRequest($request);
        if (!$manager) {
            throw $this->createNotFoundException('Manager introuvable.');
        }

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur lié au manager introuvable.');
        }
        if($confirmation->isSubmitted())
        {
            if($confirmation->isValid()){
                try {
                    $entityManager->remove($manager);
                    $entityManager->remove($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Manager supprimé avec succès.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la suppression.');
                }
                return $this->redirectToRoute('app_administration_manager');
            }
        }
        return $this->render('manager/admin/manager/detail_manager.html.twig', [
            'page' => 'administration-supprimer-manager',
            'confirmation'=>$confirmation->createView(), 
            'form'=>$form->createView(),
            'manager'=>$manager,
            'user'=>$user,
        ]);
    }
}
