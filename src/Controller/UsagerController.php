<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\CommandeRepository;
use App\Repository\UsagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur gérant les actions relatives aux usagers.
 */
#[Route('/{_locale}/usager')]
final class UsagerController extends AbstractController
{
    /**
     * Affiche les informations d'un usager spécifique.
     *
     * @param UsagerRepository $usagerRepository Repository des usagers.
     * @return Response
     */
    #[Route(
        path: '/',
        name: 'app_usager_index',
        requirements: ['_locale' => '%app.supported_locales%'],
        methods: ['GET'],
    )]
    public function index(UsagerRepository $usagerRepository): Response
    {
        return $this->render('usager/index.html.twig');
    }

    /**
     * Crée un nouvel usager et l'enregistre en base de données.
     *
     * @param Request $request Requête HTTP.
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités.
     * @param UserPasswordHasherInterface $passwordHasher Service de hachage des mots de passe.
     * @param SessionInterface $session Session utilisateur.
     * @return Response
     */
    #[Route(
        path: '/new',
        name: 'app_usager_new',
        requirements: ['_locale' => '%app.supported_locales%'],
        methods: ['GET', 'POST'],
    )]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): Response
    {
        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());
            $usager->setPassword($hashedPassword);
            $entityManager->persist($usager);
            $entityManager->flush();

            // Stocker le prénom de l'utilisateur en session
            $session->set('usager_prenom', $usager->getPrenom());

            // Redirection après enregistrement
            return $this->redirectToRoute('app_usager_index');
        }

        return $this->render('usager/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche la liste des commandes de l'usager connecté.
     *
     * @return Response
     */
    #[Route(
        path: '/commandes',
        name: 'app_usager_commandes',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function commandes(): Response
    {
        $commandes = $this->getUser()->getCommandes();

        foreach ($commandes as $commande) {
            $total = 0;
            foreach ($commande->getLigneCommandes() as $ligneCommande) {
                $total += $ligneCommande->getPrix();
            }

            $commande->total = $total;
        }

        return $this->render('usager/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * Affiche les détails d'une commande spécifique de l'usager connecté.
     *
     * @param int $id ID de la commande.
     * @param CommandeRepository $commandeRepository Repository des commandes.
     * @return Response
     */
    #[Route(
        path: '/details/{id}',
        name: 'app_usager_details',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function details(int $id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande || $commande->getUsager() !== $this->getUser()) {
            throw $this->createNotFoundException();
        }

        // Calcul du total de la commande
        $commande->total = 0;
        foreach ($commande->getLigneCommandes() as $ligneCommande) {
            $commande->total += $ligneCommande->getPrix();
        }

        return $this->render('usager/details.html.twig', [
            'commande' => $commande,
        ]);
    }
}
