<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    //PAGE DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/team-manager', name: 'app_team')]
    public function viewTeam(): Response
    {
        return $this->render('manager/team.html.twig', [
            'page' => 'team-manager',
        ]);
    }

    //PAGE DETAILS DE L'EQUIPE GERER PAR LE MANAGER
    #[Route('/detail-team-manager', name: 'app_detail_team')]
    public function viewDetailTeam(): Response
    {
        return $this->render('manager/detail_team.html.twig', [
            'page' => 'detail-team-manager',
        ]);
    }
}
