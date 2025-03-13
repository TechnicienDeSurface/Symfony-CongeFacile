<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error/404", name="error_404")
     */
    public function page404(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }

    
    //ERREUR 403
    #[Route(path: '/403', name: 'app_error-403', methods: ['GET'])]
    public function ErrorNotdenied(): Response
    {
        // Déclenche une erreur HTTP 403.
        throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette page.");
    }
}
