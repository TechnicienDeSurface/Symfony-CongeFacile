<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PersonRepository as UserRepository ;
use Doctrine\Persistence\ManagerRegistry ; 
use Doctrine\ORM\EntityRepository;

class TeamController extends AbstractController
{
    //PAGE DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/team-manager', name: 'app_team')]
    public function viewTeam(Request $request,UserRepository $repository): Response 
    {
        $limit = 10;
        $currentPage = $request->query->getInt('page', 1);
        $team = $repository->findPagination($currentPage, $limit);
        $team = $team->getResult() ; 
        $totalItems = $repository->countAll();
        $totalPages = ceil($totalItems / $limit);
        return $this->render('category/index.html.twig', ['team'=>$team, 
        'currentPage' => $currentPage,
        'itemsPerPage' => $limit,
        'totalPages' => $totalPages,
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
    public function searchNbConge(string $value, UserRepository $repository)
    {
        $user = $repository->findByNbConge($value) ; 
        if(!$user){
            throw $this->createNotFoundException('No category found for id ' . $value);
        }
    }

    // PAGE SUPPRESSION D'UN MEMBRE
    //#[Route('/delete-member/{id}', name: 'app_delete_member', methods: ['POST', 'DELETE'])]
    
}
