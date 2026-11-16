<?php

namespace App\Controller;

use App\Entity\Hairdresser;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * PROVISOIRE (module 1) : les vraies pages arrivent aux modules 2 (annuaire) et 3 (fiche).
 * Les routes existent déjà pour que les liens du menu et des cartes fonctionnent.
 */
final class DirectoryController extends AbstractController
{
    #[Route('/coiffeurs', name: 'app_directory')]
    public function index(): Response
    {
        return $this->render('directory/coming_soon.html.twig', [
            'title' => 'Trouver un coiffeur',
            'text' => "L'annuaire avec filtres et carte arrive au module 2.",
        ]);
    }

    #[Route('/coiffeurs/{slug}', name: 'app_hairdresser_show')]
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Hairdresser $hairdresser): Response
    {
        // Une fiche non validée n'est jamais visible publiquement (RM-02)
        if (!$hairdresser->isApproved()) {
            throw $this->createNotFoundException();
        }

        return $this->render('directory/coming_soon.html.twig', [
            'title' => $hairdresser->getName(),
            'text' => 'La fiche complète (galerie, prestations, avis, demande de rendez-vous) arrive au module 3.',
        ]);
    }
}
