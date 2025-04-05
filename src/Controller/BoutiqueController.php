<?php

namespace App\Controller;

use App\Repository\LigneCommandeRepository;
use App\Repository\ProduitRepository;
use App\Service\BoutiqueService;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur pour la gestion de la boutique en ligne.
 */
final class BoutiqueController extends AbstractController{
    /**
     * Page principale de la boutique affichant toutes les catégories.
     *
     * @param CategorieRepository $boutique Repository pour récupérer les catégories.
     * @return Response
     */
    #[Route('/{_locale}/boutique', name: 'app_boutique', requirements: ['_locale' => '%app.supported_locales%'])]
    public function index(CategorieRepository $boutique): Response
    {
        $categories = $boutique->findAllCategories();
        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,
        ]);
    }

    /**
     * Affiche les produits d'une catégorie spécifique.
     *
     * @param int $idCategorie Identifiant de la catégorie.
     * @param ProduitRepository $boutique Repository pour récupérer les produits.
     * @return Response
     */
    #[Route('/{_locale}/boutique/rayon/{idCategorie}', name: 'app_boutique_rayon', requirements: ['_locale' => '%app.supported_locales%'])]
    public function rayon(int $idCategorie, ProduitRepository $boutique): Response
    {
        $produits = $boutique->findProduitsByCategorie($idCategorie);
        return $this->render('boutique/rayon.html.twig', [
            'controller_name' => 'BoutiqueController',
            'produits' => $produits,
        ]);
    }

    /**
     * Recherche des produits en fonction du terme saisi par l'utilisateur.
     *
     * @param string $chercher Terme de recherche.
     * @param ProduitRepository $boutique Repository pour effectuer la recherche.
     * @return Response
     */
    #[Route('/{_locale}/boutique/chercher/{chercher}', name: 'app_boutique_chercher', requirements: ['_locale' => '%app.supported_locales%'])]
    public function chercher(string $chercher, ProduitRepository $boutique): Response
    {
        $produits = $boutique->findProduitsByLibelleOrTexte($chercher);
        return $this->render('boutique/chercher.html.twig', [
            'controller_name' => 'BoutiqueController',
            'produits' => $produits,
            'chercher' => $chercher,
        ]);
    }

    /**
     * Affiche les produits les plus vendus.
     *
     * @param LigneCommandeRepository $ligneCommandeRepository Repository pour récupérer les ventes.
     * @param ProduitRepository $produitRepository Repository pour récupérer les produits.
     * @return Response
     */
    public function topVentes(LigneCommandeRepository $ligneCommandeRepository, ProduitRepository $produitRepository): Response
    {
        $topVentes = $ligneCommandeRepository->findTopVentes();

        // Récupérer les IDs des produits les plus vendus
        $produitIds = array_column($topVentes, 'produitId');
        $produits = $produitRepository->findBy(['id' => $produitIds]);

        // Associer les produits à leurs quantités vendues
        $produitsTopVentes = [];
        foreach ($topVentes as $vente) {
            foreach ($produits as $produit) {
                if ($produit->getId() === $vente['produitId']) {
                    $produitsTopVentes[] = [
                        'produit' => $produit,
                        'quantiteTotale' => $vente['quantiteTotale'],
                    ];
                }
            }
        }

        return $this->render('topVentes.html.twig', [
            'topVentes' => $produitsTopVentes,
        ]);
    }
}
