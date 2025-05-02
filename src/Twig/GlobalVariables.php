<?php 
// src/Twig/GlobalVariables.php
namespace App\Twig;

use App\Service\RequestCounterService;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalVariables extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private Security $security,
        private RequestCounterService $requestCounterService
    ) {}

    public function getGlobals(): array
    {
        /** @var App\Entity\User $user */
        $user = $this->security->getUser();

        if ($user === null) {
            return ['nombre' => null];
        }

        return [
            'nombre' => $this->requestCounterService->getPendingRequestCount($user->getId())
        ];
    }
}
