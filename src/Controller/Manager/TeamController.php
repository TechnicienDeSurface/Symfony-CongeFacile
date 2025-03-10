<?php

namespace App\Controller\Manager;

use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PersonRepository as UserRepository ;
use App\Repository\RequestRepository as RequestRepository ;
use Doctrine\Persistence\ManagerRegistry ; 
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class TeamController extends AbstractController
{
    //PAGE DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/team-manager/{page}', name: 'app_team')]
    public function viewTeam(PersonRepository $personRepository, int $page = 1): Response 
    {

        $query = $personRepository->findByTeamMembers();

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
        ]);
    }

    //PAGE DETAILS DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/detail-team-manager/{id}', name: 'app_detail_team')]
    public function viewDetailTeam(int $id, ManagerRegistry $registry, UserRepository $repository, Request $request): Response
    {
        $user = $repository->find($id);
        if(!$user){
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

         return $this->render('category/form.html.twig', [
            'controller_name'=>'Team',
             'user' => $user,
         ]);

    }

    #[Route('/search-team-by-firstname=/{firstname}', name: 'search_by_firstname')]
    public function searchFirstname(string $firstname, UserRepository $repository)
    {
        $user = $repository->findByFirstname($firstname) ; 
        if(!$user){
            throw $this->createNotFoundException('No category found for id ' . $firstname);
        }
    }

    #[Route('/search-team-by-lastname=/{lastname}', name: 'search_by_lastname')]
    public function searchLastname(string $lastname, UserRepository $repository)
    {
        $user = $repository->findByLastname($lastname) ; 
        if(!$user){
            throw $this->createNotFoundException('No category found for id ' . $lastname);
        }
    }

    #[Route('/search-team-by-email=/{email}', name: 'search_by_email')]
    public function searchEmail(string $value, UserRepository $repository)
    {
        $user = $repository->findByEmail($value) ; 
        if(!$user){
            throw $this->createNotFoundException('No category found for id ' . $value);
        }
    }

    #[Route('/search-team-by-poste=/{poste}', name: 'search_by_poste')]
    public function searchPoste(string $value, UserRepository $repository)
    {
        $user = $repository->findByPoste($value) ; 
        if(!$user){
            throw $this->createNotFoundException('No category found for id ' . $value);
        }
    }

    #[Route('/search-team-by-nbConge=/{nbConge}', name: 'search_by_nbconge')]
    public function searchNbConge(string $value, RequestRepository $repository)
    {
        $user = $repository->finfindByCongeByCollaboratordByNbConge($value) ; 
        if(!$user){
            throw $this->createNotFoundException('No category found for id ' . $value);
        }
    }

    // PAGE SUPPRESSION D'UN MEMBRE
    //#[Route('/delete-member/{id}', name: 'app_delete_member', methods: ['POST', 'DELETE'])]
    
}
