<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;

class ArticleSlugTest extends TestCase
{
    public function testSlugGenereDepuisTitre(): void
    {
        $article = new Article();
        $article->setTitre('Mon premier article');
        $article->generateSlug();

        $this->assertSame('mon-premier-article', $article->getSlug());
    }

    public function testSlugManuelNonEcrase(): void
    {
        $article = new Article();
        $article->setTitre('Mon premier article');
        $article->setSlug('slug-perso');
        $article->generateSlug();

        $this->assertSame('slug-perso', $article->getSlug());
    }

    public function testCaracteresSpeciauxNormalises(): void
    {
        $article = new Article();
        $article->setTitre('Ça & Là : "test"');
        $article->generateSlug();

        $this->assertSame('ca-la-test', $article->getSlug());
    }

    public function testTitreNullProduitsSlugNull(): void
    {
        $article = new Article();
        $article->generateSlug();

        $this->assertNull($article->getSlug());
    }
}
