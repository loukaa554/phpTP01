<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant l'authentification des utilisateurs.
 */
#[Route('/{_locale}')]
class SecurityController extends AbstractController
{
    /**
     * Affiche la page de connexion et traite les erreurs éventuelles.
     *
     * @param AuthenticationUtils $authenticationUtils Utilitaire d'authentification pour récupérer l'erreur et le dernier identifiant utilisé.
     * @param Request $request Requête HTTP.
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Déconnecte l'utilisateur (ceci est intercepté par le firewall de Symfony).
     *
     * @throws \LogicException Cette méthode ne doit jamais être appelée directement.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode peut rester vide - elle est interceptée par le firewall de Symfony.');
    }
}
