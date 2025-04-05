<?php

namespace App\Controller;

use App\Service\ChangeMonnaieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur par défaut de l'application.
 */
class DefaultController extends AbstractController
{
    /**
     * Redirige vers la page d'accueil de l'application.
     *
     * @return Response
     */
    #[Route(
        path: '/',
        name: 'app_index',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function home(): Response
    {
        return $this->redirectToRoute('app_default_index');
    }

    /**
     * Affiche la page d'accueil.
     *
     * @return Response
     */
    #[Route(
        path: '/{_locale}',
        name: 'app_default_index',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * Affiche la page de contact.
     *
     * @return Response
     */
    #[Route(
        path: '/{_locale}/contact',
        name: 'app_default_contact',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig');
    }
}
