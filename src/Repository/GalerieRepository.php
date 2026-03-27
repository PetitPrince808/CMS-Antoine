<?php

namespace App\Repository;

use App\Entity\Galerie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Galerie.
 *
 * @extends ServiceEntityRepository<Galerie>
 */
class GalerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Galerie::class);
    }
}
