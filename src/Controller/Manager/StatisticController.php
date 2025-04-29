<?php

namespace App\Controller\Manager;

use App\Entity\Request as RequestEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatisticController extends AbstractController
{
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/statistic-manager', name: 'app_statistic')]
    public function viewStatistic(EntityManagerInterface $entityManagerInterface): Response
    {
        $requests = $entityManagerInterface->getRepository(RequestEntity::class)->findBy([], []);
        $requestCounts = [];

        //GRAPHIQUE EN CAMEMBERT
        foreach ($requests as $requestEntity) {
            $type = $requestEntity->getRequestType()?->getName();
            if ($type) {
                $requestCounts[$type] = ($requestCounts[$type] ?? 0) + 1;
            }
        }

        $data = [
            'labels' => array_keys($requestCounts),
            'values' => array_values($requestCounts),
        ];

        //GRAPHIQUE EN COURBE
        $monthlyCounts = array_fill(0, 12, 0);//NOMBRE DE MOIS
        $totalCounts = array_fill(0, 12, 0);//NOMBRE DE TOTAL PAR MOIS

        foreach ($requests as $requestEntity) {
            $answerAt = $requestEntity->getAnswerAt();//RECUPÈRE LA DATE DES RÉPONSES
            if ($answerAt) {
                $month = $answerAt->format('n') - 1; //-1 car commence à 0
                $totalCounts[$month]++;
                
                if ($requestEntity->isAnswer()) {
                    $monthlyCounts[$month]++;
                }
            }
        }

        //CALCUL DU POURCENTAGE D'ACCEPTATION PAR MOIS
        $percentageCounts = [];
        for ($i = 0; $i < 12; $i++) {
            if ($totalCounts[$i] > 0) {
                $percentageCounts[] = round(($monthlyCounts[$i] / $totalCounts[$i]) * 100, 2);
            } else {
                $percentageCounts[] = 0; //SI AUCUNE DEMANDE
            }
        }

        $dataAccepted = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'values' => $percentageCounts,
        ];

        return $this->render('manager/statistic.html.twig', [
            'page' => 'statistic-manager',
            'requestData' => json_encode($data),
            'dataAccepted' => json_encode($dataAccepted),
        ]);
    }
}
