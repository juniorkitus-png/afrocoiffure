<?php

namespace App\Repository;

use App\Entity\Hairdresser;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hairdresser>
 */
class HairdresserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hairdresser::class);
    }

    /**
     * Coiffeurs validés les mieux notés, pour l'accueil (F-02).
     *
     * Chaque ligne contient : [0] => Hairdresser, 'avgRating', 'reviewCount', 'minPrice'.
     * Les sous-requêtes évitent de multiplier les lignes (avis × prestations).
     *
     * @return array<int, array{0: Hairdresser, avgRating: ?string, reviewCount: int|string, minPrice: ?string}>
     */
    public function findFeatured(int $limit = 6): array
    {
        return $this->createQueryBuilder('h')
            ->select('h')
            ->addSelect('(SELECT AVG(r1.rating) FROM App\Entity\Review r1 WHERE r1.hairdresser = h AND r1.status = :published) AS avgRating')
            ->addSelect('(SELECT COUNT(r2.id) FROM App\Entity\Review r2 WHERE r2.hairdresser = h AND r2.status = :published) AS reviewCount')
            ->addSelect('(SELECT MIN(s.price) FROM App\Entity\Service s WHERE s.hairdresser = h) AS minPrice')
            ->where('h.status = :approved')
            ->setParameter('approved', Hairdresser::STATUS_APPROVED)
            ->setParameter('published', Review::STATUS_PUBLISHED)
            ->orderBy('avgRating', 'DESC')
            ->addOrderBy('reviewCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Villes où au moins un coiffeur validé exerce, avec leur nombre.
     *
     * @return array<int, array{city: string, total: int|string}>
     */
    public function findCitiesWithCount(): array
    {
        return $this->createQueryBuilder('h')
            ->select('h.city AS city, COUNT(h.id) AS total')
            ->where('h.status = :approved')
            ->setParameter('approved', Hairdresser::STATUS_APPROVED)
            ->groupBy('h.city')
            ->orderBy('total', 'DESC')
            ->addOrderBy('h.city', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function countApproved(): int
    {
        return $this->count(['status' => Hairdresser::STATUS_APPROVED]);
    }
}
