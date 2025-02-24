<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController{
    #[Route('/{_locale}/panier', name: 'app_panier')]
    public function index(PanierService $panier): Response
    {
        $content = $panier->getContenu();
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panier' => $content,
            'total' => $panier->getTotal(),
            'quantite' => $panier->getNombreProduits(),
        ]);
    }
    #[Route('/{_locale}/panier/ajouter/{idProduit}/{quantite}', name: 'app_panier_ajouter')]
    public function ajouter(int $idProduit,int $quantite, PanierService $panier): Response
    {
        $panier->ajouterProduit($idProduit, $quantite);
        $content = $panier->getContenu();
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panier' => $content,
            'total' => $panier->getTotal(),
            'quantite' => $panier->getNombreProduits(),
        ]);
    }
    #[Route('/{_locale}/panier/enlever/{idProduit}/{quantite}', name: 'app_panier_enlever')]
    public function enlever(int $idProduit,int $quantite, PanierService $panier): Response
    {
        $panier->enleverProduit($idProduit, $quantite);
        $content = $panier->getContenu();
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panier' => $content,
            'total' => $panier->getTotal(),
            'quantite' => $panier->getNombreProduits(),
        ]);
    }
    #[Route('/{_locale}/panier/supprimer/{idProduit}', name: 'app_panier_supprimer')]
    public function supprimer(int $idProduit, PanierService $panier): Response
    {
        $panier->supprimerProduit($idProduit);
        $content = $panier->getContenu();
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panier' => $content,
            'total' => $panier->getTotal(),
            'quantite' => $panier->getNombreProduits(),
        ]);
    }
    #[Route('/{_locale}/panier/vider', name: 'app_panier_vider')]
    public function vider(PanierService $panier): Response
    {
        $panier->vider();
        $content = $panier->getContenu();
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panier' => $content,
            'total' => $panier->getTotal(),
            'quantite' => $panier->getNombreProduits(),
        ]);
    }
}
