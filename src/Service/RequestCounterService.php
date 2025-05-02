<?php 
// src/Service/RequestCounterService.php

namespace App\Service;

use App\Repository\RequestRepository;

class RequestCounterService
{
    public RequestRepository $requestRepository;

    public function __construct(RequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function getPendingRequestCount(int $managerId): int
    {
        // Appel à la méthode de comptage des demandes en attente pour le manager
        return $this->requestRepository->findRequestPendingByManager($managerId);
    }
}
