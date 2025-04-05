<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Usager;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Service permettant de manipuler un panier d'achats en session.
 */
class PanierService
{
    private SessionInterface $session; // Service pour manipuler la session.
    private ProduitRepository $boutique; // Service permettant d'accéder aux produits.
    private array $panier; // Contient les produits en panier (idProduit => quantité).

    private const PANIER_SESSION = 'panier'; // Clé de session pour stocker le panier.

    /**
     * Constructeur du service Panier.
     *
     * @param RequestStack $requestStack Service permettant d'accéder à la session.
     * @param ProduitRepository $boutique Repository permettant de récupérer les produits.
     */
    public function __construct(RequestStack $requestStack, ProduitRepository $boutique)
    {
        $this->boutique = $boutique;
        $this->session = $requestStack->getSession();
        $this->panier = $this->session->get(self::PANIER_SESSION, []);
    }

    /**
     * Retourne le montant total du panier.
     * Le total est calculé en multipliant le prix de chaque produit par sa quantité.
     *
     * @return float Montant total du panier.
     */
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->panier as $idProduit => $quantity) {
            $produit = $this->boutique->findProduitById($idProduit);
            if ($produit) {
                $total += $produit->getPrix() * $quantity;
            }
        }
        return $total;
    }

    /**
     * Retourne le nombre total de produits dans le panier.
     *
     * @return int Nombre de produits dans le panier.
     */
    public function getNombreProduits(): int
    {
        return array_sum($this->panier); // Calcul de la somme de toutes les quantités.
    }

    /**
     * Ajoute un produit au panier.
     * Si le produit existe déjà, la quantité est incrémentée, sinon il est ajouté avec la quantité spécifiée.
     *
     * @param int $idProduit L'ID du produit à ajouter.
     * @param int $quantite La quantité de produit à ajouter (par défaut 1).
     */
    public function ajouterProduit(int $idProduit, int $quantite = 1): void
    {
        $this->panier[$idProduit] = ($this->panier[$idProduit] ?? 0) + $quantite;
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    /**
     * Enlève une quantité donnée d'un produit du panier.
     * Si la quantité devient inférieure ou égale à 0, le produit est supprimé du panier.
     *
     * @param int $idProduit L'ID du produit à enlever.
     * @param int $quantite La quantité à enlever (par défaut 1).
     */
    public function enleverProduit(int $idProduit, int $quantite = 1): void
    {
        if (isset($this->panier[$idProduit])) {
            $this->panier[$idProduit] -= $quantite;
            if ($this->panier[$idProduit] <= 0) {
                unset($this->panier[$idProduit]); // Si la quantité est <= 0, on enlève le produit.
            }
            $this->session->set(self::PANIER_SESSION, $this->panier);
        }
    }

    /**
     * Supprime un produit du panier.
     *
     * @param int $idProduit L'ID du produit à supprimer.
     */
    public function supprimerProduit(int $idProduit): void
    {
        unset($this->panier[$idProduit]);
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    /**
     * Vide complètement le panier.
     */
    public function vider(): void
    {
        $this->panier = [];
        $this->session->set(self::PANIER_SESSION, []);
    }

    /**
     * Renvoie le contenu détaillé du panier sous forme de tableau.
     * Chaque élément contient le produit et sa quantité.
     *
     * @return array Tableau des produits avec leur quantité.
     */
    public function getContenu(): array
    {
        $contenu = [];
        foreach ($this->panier as $idProduit => $quantite) {
            $produit = $this->boutique->findProduitById($idProduit);
            if ($produit) {
                $contenu[] = ['produit' => $produit, 'quantite' => $quantite];
            }
        }
        return $contenu;
    }

    /**
     * Convertit le contenu du panier en une commande pour un usager.
     * Crée une nouvelle commande, associe les produits du panier et les ajoute à la base de données.
     * Une fois la commande créée, le panier est vidé.
     *
     * @param Usager $usager L'usager qui passe la commande.
     * @param EntityManagerInterface $entityManager L'EntityManager pour persister les données.
     *
     * @return Commande|null Retourne la commande créée ou null si le panier est vide.
     */
    public function panierToCommande(Usager $usager, EntityManagerInterface $entityManager): ?Commande
    {
        if (empty($this->panier)) {
            return null; // Si le panier est vide, on ne crée pas de commande.
        }

        // Création de la commande
        $commande = new Commande();
        $commande->setUsager($usager);
        $commande->setDateCreation(new \DateTime());

        // Ajout des lignes de commande
        foreach ($this->panier as $idProduit => $quantite) {
            $produit = $this->boutique->findProduitById($idProduit);
            if ($produit) {
                $ligneCommande = new LigneCommande();
                $ligneCommande->setProduit($produit);
                $ligneCommande->setCommande($commande);
                $ligneCommande->setQuantite($quantite);
                $ligneCommande->setPrix($produit->getPrix() * $quantite);

                $entityManager->persist($ligneCommande);
                $commande->addLigneCommande($ligneCommande);
            }
        }

        // Persistance de la commande
        $entityManager->persist($commande);
        $entityManager->flush();

        // Vide le panier après la création de la commande
        $this->vider();

        return $commande;
    }
}
