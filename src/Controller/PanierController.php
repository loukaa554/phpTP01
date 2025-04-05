<?php

namespace App\Controller;

use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur gérant les fonctionnalités du panier.
 */
#[Route('/{_locale}/panier')]
final class PanierController extends AbstractController{

    private PanierService $panierService;

    /**
     * Constructeur du contrôleur du panier.
     *
     * @param PanierService $panierService Service de gestion du panier.
     */
    public function __construct(PanierService $panierService)
    {
        $this->panierService = $panierService;
    }

    /**
     * Affiche le contenu du panier.
     *
     * @param PanierService $panier Service de gestion du panier.
     * @return Response
     */
    #[Route('/', name: 'app_panier')]
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

    /**
     * Retourne le nombre total de produits dans le panier.
     *
     * @return Response
     */
    public function nombreProduits(): Response
    {
        return new Response($this->panierService->getNombreProduits());
    }

    /**
     * Ajoute un produit au panier.
     *
     * @param int $idProduit ID du produit.
     * @param int $quantite Quantité à ajouter.
     * @param PanierService $panier Service du panier.
     * @return Response
     */
    #[Route('/ajouter/{idProduit}/{quantite}', name: 'app_panier_ajouter')]
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

    /**
     * Enlève une quantité spécifique d'un produit du panier.
     *
     * @param int $idProduit ID du produit.
     * @param int $quantite Quantité à enlever.
     * @param PanierService $panier Service du panier.
     * @return Response
     */
    #[Route('/enlever/{idProduit}/{quantite}', name: 'app_panier_enlever')]
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

    /**
     * Supprime un produit du panier.
     *
     * @param int $idProduit ID du produit.
     * @param PanierService $panier Service du panier.
     * @return Response
     */
    #[Route('/supprimer/{idProduit}', name: 'app_panier_supprimer')]
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

    /**
     * Vide complètement le panier.
     *
     * @param PanierService $panier Service du panier.
     * @return Response
     */
    #[Route('/vider', name: 'app_panier_vider')]
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

    /**
     * Convertit le panier en commande et l'associe à l'utilisateur authentifié.
     *
     * @param PanierService $panierService Service du panier.
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités.
     * @return Response
     */
    #[Route('/commander', name: 'app_panier_commander')]
    public function commander(PanierService $panierService, EntityManagerInterface $entityManager): Response
    {
        $usager = $this->getUser();

        if (!$usager) {
            return $this->redirectToRoute('app_login');
        }

        // Convertir le panier en commande
        $commande = $panierService->panierToCommande($usager, $entityManager);

        if (!$commande) {
            return $this->render('panier/commande.html.twig', [
                'message' => 'Votre panier est vide. Aucune commande n\'a été créée.',
            ]);
        }

        return $this->render('panier/commande.html.twig', [
            'usager' => $usager,
            'commande' => $commande,
            'idCommande' => $commande->getId(),
        ]);
    }
}
