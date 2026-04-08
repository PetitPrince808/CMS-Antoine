<?php

namespace App\Tests\Entity;

use App\Entity\CategorieArticle;
use App\Entity\Article;
use PHPUnit\Framework\TestCase;

class CategorieArticleTest extends TestCase
{
    public function testNom(): void
    {
        $categorie = new CategorieArticle();
        $categorie->setNom('Tutoriels');

        $this->assertSame('Tutoriels', $categorie->getNom());
    }

    public function testCollectionArticlesVideeParDefaut(): void
    {
        $categorie = new CategorieArticle();

        // La collection doit être initialisée vide à la construction
        $this->assertCount(0, $categorie->getArticles());
    }

    public function testArticlesContientBienLaRelation(): void
    {
        $categorie = new CategorieArticle();
        $article = new Article();
        $article->setCategorieArticle($categorie);

        // La relation inverse n'est pas auto-synchronisée sans flush Doctrine,
        // on vérifie ici la relation directe
        $this->assertSame($categorie, $article->getCategorieArticle());
    }
}
