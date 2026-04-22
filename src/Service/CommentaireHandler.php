<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Gère la soumission de commentaires : association, statut initial et persistance.
 */
class CommentaireHandler
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * Associe l'auteur et l'article au commentaire, force le statut
     * en_attente (modération EasyAdmin) puis persiste en base.
     */
    public function submit(Commentaire $commentaire, Article $article, User $auteur): void
    {
        $commentaire->setArticle($article);
        $commentaire->setAuteur($auteur);
        $commentaire->setStatut('en_attente');

        $this->em->persist($commentaire);
        $this->em->flush();
    }
}
