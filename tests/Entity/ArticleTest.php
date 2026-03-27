<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\CategorieArticle;
use App\Entity\Tag;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testStatutParDefaut(): void
    {
        $article = new Article();
        // Un article créé sans statut doit être en brouillon par défaut
        $this->assertSame('brouillon', $article->getStatut());
    }

    public function testAjoutTag(): void
    {
        $article = new Article();
        $tag = new Tag();
        $tag->setNom('Symfony');

        $article->addTag($tag);

        $this->assertCount(1, $article->getTags());
        $this->assertTrue($article->getTags()->contains($tag));
    }

    public function testCategorie(): void
    {
        $article = new Article();
        $categorie = new CategorieArticle();
        $categorie->setNom('Tutoriels');

        $article->setCategorieArticle($categorie);

        $this->assertSame('Tutoriels', $article->getCategorieArticle()->getNom());
    }

    public function testAuteur(): void
    {
        $article = new Article();
        $user = new User();
        $user->setNom('Antoine');

        $article->setAuteur($user);

        $this->assertSame('Antoine', $article->getAuteur()->getNom());
    }
}
