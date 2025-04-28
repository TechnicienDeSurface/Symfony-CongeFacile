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
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class ManagerController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){
        
    }

    //PAGE MANAGER VIA ADMINISTRATION MANAGER
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
                        $this->addFlash('error', 'Erreur pour l\'ajout du manager'); 
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
                        $this->addFlash('success', 'Succès pour ajouter de l\'utilisateur');
                        return $this->redirectToRoute('app_administration_manager');
                    }catch(\Exception $e ){
                        $this->addFlash('error', 'Erreur pour l\'ajout de l\'utilisateur'); 
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
                if(empty($formData['newPassword']) && empty($formData['confirmPassword'])){
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
                        $this->addFlash('success', 'Succès pour la mise à jour du manager');
                    }catch(\Exception $e){

                    }try{
                        if($user->getEmail() != $formData['email']){
                            $user->setEmail($formData['email']);
                        } 
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->addFlash('success', 'Succès pour la mise à jour de l\'utilisateur');
                        return $this->redirectToRoute('app_administration_manager');
                    }catch(\Exception $e ){
                        $this->addFlash('error', 'Erreur pour la mise à jour de l\'utilisateur'); 
                    }
                }else{
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
                            $this->addFlash('success', 'Succès pour la mise à jour de l\'utilisateur');
                            return $this->redirectToRoute('app_administration_manager');
                        }catch(\Exception $e ){
                            $this->addFlash('error', 'Erreur pour la mise à jour de l\'utilisateur'); 
                        }
                    }else{
                        $this->addFlash('error','Confirmation mot de passe incorrect');
                    }
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
}
