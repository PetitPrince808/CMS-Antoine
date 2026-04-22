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
     * Retourne les X derniers articles publiés.
     *
     * @return Article[]
     */
    public function findLatest(int $limit = 3): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.statut = :statut')
            ->setParameter('statut', 'publie')
            ->orderBy('a.datePublication', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des articles publiés par mot-clé.
     *
     * @return Article[]
     */
    public function search(string $query): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.statut = :statut')
            ->andWhere('a.titre LIKE :query OR a.contenu LIKE :query')
            ->setParameter('statut', 'publie')
            ->setParameter('query', '%' . $query . '%')
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
