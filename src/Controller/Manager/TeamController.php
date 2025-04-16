<?php

namespace App\Controller\Manager;

use App\Entity\User;
use App\Entity\Person;
use App\Form\FilterManagerTeamFormType;
use App\Form\CollaborateurType;
use App\Repository\PersonRepository;
use App\Repository\PositionRepository;
use App\Repository\UserRepository;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RequestRepository as RequestRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TeamController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        
    }

    //PAGE DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/team-manager/{page}', name: 'app_team', methods: ['GET', 'POST'])]
    public function viewTeam(Request $request , PersonRepository $personRepository, int $page = 1): Response 
    {
        $form = $this->createForm(FilterManagerTeamFormType::class);
        $form->handleRequest($request);

        $filters = [
            'last_name'         => $request->query->get('lastname'),
            'first_name'        => $request->query->get('firstname'),
            'email'             => $request->query->get('email'),
            'name'              => $request->query->get('name'),
            'totalleavedays'    => $request->query->get('totalleavedays'),
        ];

        // Si le formulaire est soumis et valide, on utilise ses données
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        $order = $filters['totalleavedays'] ?? '';

        // Recherche dans le repository avec les filtres
        $query = $personRepository->searchTeamMembers($filters, $order);
        
        // Pagination avec QueryAdapter
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);

        try{
            $pagerfanta->setCurrentPage($page);
        }
        catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $e) {
            throw $this->createNotFoundException('La page demandée n\'existe pas.');
        }

        return $this->render('manager/team.html.twig', [
            'pager' => $pagerfanta,
            'team' => $pagerfanta->getCurrentPageResults(),
            'filters' => $filters,
            'form' => $form->createView(),
        ]);
    }

    //PAGE DETAILS DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/detail-team-manager/{id}', name: 'app_detail_team', methods: ['POST'])]
    public function viewDetailTeam(int $id, ManagerRegistry $registry, UserRepository $repository, Request $request): Response
    {
        $user = $repository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        return $this->render('manager/detail_team.html.twig', [
            'controller_name' => 'Team',
            'user' => $user,
        ]);
    }

    //PAGE AJOUTER MANAGER VIA ADMINISTRATION MANAGER
    #[Route('/administration-ajouter-collaborateur', name: 'app_administration_ajouter_collaborateur')]
    public function addCollaborateur(UserPasswordHasherInterface $hash, ManagerRegistry $registry, Security $security,Request $request, PositionRepository $position_repository, PersonRepository $person_repository, DepartmentRepository $department_repository, UserRepository $user_repository): Response
    {
        $manager = New User() ; 
        $manager = $security->getUser() ;
        if (!$manager instanceof \App\Entity\User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de App\Entity\User.');
        }
        $form = $this->createForm(CollaborateurType::class) ; 
        $form->handleRequest($request) ; 
        if($form->isSubmitted())
        {
            if($form->isValid()){
                try{
                    $formData = $form->getData();
                    $new_collaborator= new Person();
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
                }catch(\Exception $e){
                    $this->addFlash('error', 'Erreur pour l\'ajout de collaborateur'); 
                }try{
                    $person = $registry->getManager()->getRepository(Person::class)->find($new_collaborator->getId());
                    $new_user = New User();
                    $new_user->setEmail($formData['email']);
                    $password_hash = $this->passwordHasher->hashPassword($new_user, $formData['newPassword']) ;
                    $new_user->setPassword($password_hash);
                    $new_user->setPerson($person);
                    $new_user->setRoles([1 => "ROLE_COLLABORATEUR"]);
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
        
        return $this->render('manager/add_collaborator.html.twig', [
            'page' => 'administration-ajouter-collaborateur',
            'manager'=>$manager,
            'form'=>$form,
        ]);
    }


    // PAGE SUPPRESSION D'UN MEMBRE
    //#[Route('/delete-member/{id}', name: 'app_delete_member', methods: ['POST', 'DELETE'])]

}
