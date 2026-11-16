<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\CategoryRepository;
use App\Repository\HairdresserRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * F-02 : accueil avec coiffeurs et spécialités mis en avant.
 */
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        CategoryRepository $categoryRepository,
        HairdresserRepository $hairdresserRepository,
        ReviewRepository $reviewRepository,
    ): Response {
        $cities = $hairdresserRepository->findCitiesWithCount();

        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['id' => 'ASC']),
            'featured' => $hairdresserRepository->findFeatured(6),
            'cities' => $cities,
            'stats' => [
                'hairdressers' => $hairdresserRepository->countApproved(),
                'cities' => \count($cities),
                'reviews' => $reviewRepository->count(['status' => Review::STATUS_PUBLISHED]),
            ],
        ]);
    }
}
