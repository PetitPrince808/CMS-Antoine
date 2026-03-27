<?php

namespace App\Repository;

use App\Entity\CategoriePage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité CategoriePage.
 *
 * @extends ServiceEntityRepository<CategoriePage>
 */
class CategoriePageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoriePage::class);
    }
}
