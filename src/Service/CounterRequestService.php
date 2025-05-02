<?php 
namespace App\Service;

use App\Repository\RequestRepository;

class RequestCounterService
{
    private RequestRepository $requestRepository;

    public function __construct(RequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function getPendingRequestCount(): int
    {
        return $this->requestRepository->countPendingRequests();
    }
}
