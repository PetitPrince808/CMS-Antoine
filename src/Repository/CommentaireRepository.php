<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Commentaire.
 *
 * @extends ServiceEntityRepository<Commentaire>
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    /**
     * Retourne les commentaires approuvés d'un article, du plus ancien au plus récent.
     *
     * @return Commentaire[]
     */
    public function findApprovedByArticle(Article $article): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->andWhere('c.statut = :statut')
            ->setParameter('article', $article)
            ->setParameter('statut', 'approuve')
            ->orderBy('c.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
