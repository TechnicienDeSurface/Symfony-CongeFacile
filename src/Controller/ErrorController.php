<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


class ErrorController extends AbstractController
{
    /**
     * @Route("/error/404", name="error_404")
     */
    public function page404(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }

    
    // ERREUR 403
    #[Route(path: '/403', name: 'app_error-403', methods: ['GET'])]
    public function ErrorNotdenied(): Response
    {
        // Déclenche une erreur HTTP 403.
        throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette page.");
    }

    // ERREUR 500
    #[Route(path: '/500', name: 'app_error-500', methods: ['GET'])]
    public function ErrorServer(): Response
    {
        // Déclenche une erreur HTTP 500.
        return $this->render('bundles/TwigBundle/Exception/error500.html.twig');
    }

        public function __invoke(\Exception $exception): Response
        {
            // Vérifier si l'exception est une instance de HttpExceptionInterface
            if ($exception instanceof HttpExceptionInterface) {
                $statusCode = $exception->getStatusCode();
            } else {
                // Dans tous les autres cas, définir 500 (erreur serveur interne)
                $statusCode = 500;
            }
                
            // Gérer les erreurs spécifiques en fonction du code d'état
            switch ($statusCode) {
                case 404:
                    return $this->page404();
                case 403:
                    return $this->ErrorNotdenied();
                case 500:
                default:
                    return $this->ErrorServer();
            }
        }
        
}
