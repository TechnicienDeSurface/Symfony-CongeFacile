<?php

namespace App\Manager\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticController extends AbstractController
{
    #[Route('/statistic/contoller', name: 'app_statistic_contoller')]
    public function index(): Response
    {
        return $this->render('statistic_contoller/index.html.twig', [
            'controller_name' => 'StatisticContollerController',
        ]);
    }
}
