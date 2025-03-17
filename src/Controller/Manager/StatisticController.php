<?php

namespace App\Controller\Manager;

use App\Entity\Request as RequestEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticController extends AbstractController
{
    #[Route('/statistic-manager', name: 'app_statistic')]
    public function viewStatistic(EntityManagerInterface $entityManagerInterface): Response
    {
        $requests = $entityManagerInterface->getRepository(RequestEntity::class)->findAll();
        $requestCounts = [];

        // Compter les types de demandes
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

        return $this->render('manager/statistic.html.twig', [
            'page' => 'statistic-manager',
            'requestData' => json_encode($data),
        ]);
    }
}
