<?php

namespace App\Controller\Manager;

use App\Entity\User;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PersonRepository as UserRepository;
use App\Repository\RequestRepository as RequestRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\SecurityBundle\SecurityBundle;

class TeamController extends AbstractController
{
    //PAGE DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/team-manager/{page}', name: 'app_team', methods: ['GET', 'POST'])]
    public function viewTeam(Request $request , PersonRepository $personRepository, int $page = 1): Response 
    {

        $filters = [
            'last_name'     => $request->query->get('last_name'),
            'first_name'    => $request->query->get('first_name'),
            'email'         => $request->query->get('email'),
            'position_name' => $request->query->get('position_name'),
            //ATTENTION, MANQUE LE FILTRE PAR LE NOMBRE DE CONGE
        ];

        $query = $personRepository->searchTeamMembers($filters);

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
        ]);
    }

    //PAGE DETAILS DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/detail-team-manager/{id}', name: 'app_detail_team')]
    public function viewDetailTeam(int $id, ManagerRegistry $registry, UserRepository $repository, Request $request): Response
    {
        $user = $repository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        return $this->render('category/form.html.twig', [
            'controller_name' => 'Team',
            'user' => $user,
        ]);
    }

    // PAGE SUPPRESSION D'UN MEMBRE
    //#[Route('/delete-member/{id}', name: 'app_delete_member', methods: ['POST', 'DELETE'])]

}
