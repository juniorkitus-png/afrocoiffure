<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * F-08 : pages d'information et pages légales (contenu statique).
 */
final class PageController extends AbstractController
{
    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('pages/a_propos.html.twig');
    }

    #[Route('/comment-ca-marche', name: 'app_how')]
    public function how(): Response
    {
        return $this->render('pages/comment_ca_marche.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('pages/mentions_legales.html.twig');
    }

    #[Route('/confidentialite', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('pages/confidentialite.html.twig');
    }
}
