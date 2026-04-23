<?php

namespace App\Repository;

use App\Entity\CategorieGalerie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité CategorieGalerie.
 *
 * @extends ServiceEntityRepository<CategorieGalerie>
 */
class CategorieGalerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieGalerie::class);
    }
}
