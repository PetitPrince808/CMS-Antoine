<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Page.
 *
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * Retourne toutes les pages racines publiées (sans parent),
     * triées par titre pour un menu stable.
     *
     * @return Page[]
     */
    public function findPublishedRoots(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.statut = :statut')
            ->andWhere('p.parent IS NULL')
            ->setParameter('statut', 'publie')
            ->orderBy('p.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une page publiée par son slug.
     * Retourne null si le slug n'existe pas ou si la page n'est pas publiée.
     */
    public function findOnePublishedBySlug(string $slug): ?Page
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.statut = :statut')
            ->setParameter('slug', $slug)
            ->setParameter('statut', 'publie')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
