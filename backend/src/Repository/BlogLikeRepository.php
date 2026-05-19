<?php

namespace App\Repository;

use App\Entity\BlogLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<BlogLike> */
class BlogLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogLike::class);
    }

    public function countByArticle(string $slug): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->andWhere('l.articleSlug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findOneByArticleAndLiker(string $slug, string $likerKey): ?BlogLike
    {
        return $this->findOneBy([
            'articleSlug' => $slug,
            'likerKey' => $likerKey,
        ]);
    }

    /** @return array<string, int> slug => count */
    public function countBySlugs(array $slugs): array
    {
        if ($slugs === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('l')
            ->select('l.articleSlug AS slug, COUNT(l.id) AS cnt')
            ->andWhere('l.articleSlug IN (:slugs)')
            ->setParameter('slugs', $slugs)
            ->groupBy('l.articleSlug')
            ->getQuery()
            ->getArrayResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string) $row['slug']] = (int) $row['cnt'];
        }

        return $counts;
    }
}
