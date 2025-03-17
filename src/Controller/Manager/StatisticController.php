<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticController extends AbstractController
{
    //PAGE DES STATISTIQUES DE CONGES ACCEPTER OU REFUSER
    #[Route('/statistic-manager', name: 'app_statistic')]
    public function viewStatistic(): Response
    {
        $data = [
            'labels' => ['Catégorie A', 'Catégorie B', 'Catégorie C'],
            'values' => [30, 50, 20]
        ];

        return $this->render('manager/statistic.html.twig', [
            'page' => 'statistic-manager',
            'chartsCamembert' => json_encode($data),
        ]);
    }
}
