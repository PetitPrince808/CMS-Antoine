<?php

namespace App\Tests\Entity;

use App\Entity\Commentaire;
use App\Entity\Article;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentaireTest extends TestCase
{
    public function testStatutParDefaut(): void
    {
        $commentaire = new Commentaire();

        // Tout commentaire créé doit attendre modération avant d'être visible
        $this->assertSame('en_attente', $commentaire->getStatut());
    }

    public function testDateInitialiseeALaConstruction(): void
    {
        $commentaire = new Commentaire();

        // La date doit être auto-remplie à la création pour éviter les nulls en base
        $this->assertInstanceOf(\DateTimeInterface::class, $commentaire->getDate());
    }

    public function testContenu(): void
    {
        $commentaire = new Commentaire();
        $commentaire->setContenu('Super article !');

        $this->assertSame('Super article !', $commentaire->getContenu());
    }

    public function testLienAvecArticle(): void
    {
        $commentaire = new Commentaire();
        $article = new Article();
        $article->setTitre('Mon article');

        $commentaire->setArticle($article);

        $this->assertSame($article, $commentaire->getArticle());
    }

    public function testLienAvecAuteur(): void
    {
        $commentaire = new Commentaire();
        $user = new User();
        $user->setNom('Antoine');

        $commentaire->setAuteur($user);

        $this->assertSame('Antoine', $commentaire->getAuteur()->getNom());
    }
}
