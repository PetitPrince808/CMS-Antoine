<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Article.
 *
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Retourne tous les articles publiés, du plus récent au plus ancien.
     *
     * @return Article[]
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.statut = :statut')
            ->setParameter('statut', 'publie')
            ->orderBy('a.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne un article publié par son slug, ou null s'il n'existe pas.
     */
    public function findOnePublishedBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.statut = :statut')
            ->andWhere('a.slug = :slug')
            ->setParameter('statut', 'publie')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
