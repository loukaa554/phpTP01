<?php

namespace App\Controller;

use App\Service\BoutiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BoutiqueController extends AbstractController{
    #[Route('/{_locale}/boutique', name: 'app_boutique', requirements: ['_locale' => '%app.supported_locales%'])]
    public function index(BoutiqueService $boutique): Response
    {
        $categories = $boutique->findAllCategories();
        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,

        ]);
    }
    #[Route('/{_locale}/boutique/rayon/{idCategorie}', name: 'app_boutique_rayon', requirements: ['_locale' => '%app.supported_locales%'])]
    public function rayon(int $idCategorie, BoutiqueService $boutique): Response
    {
        $produits = $boutique->findProduitsByCategorie($idCategorie);
        return $this->render('boutique/rayon.html.twig', [
            'controller_name' => 'BoutiqueController',
            'produits' => $produits,
        ]);
    }
    #[Route('/{_locale}/boutique/chercher/{chercher}', name: 'app_boutique_chercher', requirements: ['_locale' => '%app.supported_locales%'])]
    public function chercher(string $chercher, BoutiqueService $boutique): Response
    {
        $produits = $boutique->findProduitsByLibelleOrTexte($chercher);
        return $this->render('boutique/chercher.html.twig', [
            'controller_name' => 'BoutiqueController',
            'produits' => $produits,
            'chercher' => $chercher,
        ]);
    }
}
