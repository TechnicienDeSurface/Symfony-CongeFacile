<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorController extends AbstractController
{
    //ERREUR 404
    #[Route(path: '/error-404', name: 'app_error-404', methods: ['GET'])]
    public function ViewErreur404(): Response
    {
        // Déclenche une erreur HTTP 404.
        throw new NotFoundHttpException("La page demandée n'a pas été trouvée.");
    }
    
    //ERREUR 403
    #[Route(path: '/error-403', name: 'app_error-403', methods: ['GET'])]
    public function ErrorNotdenied(): Response
    {
        // Déclenche une erreur HTTP 403.
        throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette page.");
    }
}
