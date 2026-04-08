<?php

namespace App\Tests\Entity;

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testNom(): void
    {
        $tag = new Tag();
        $tag->setNom('Symfony');

        $this->assertSame('Symfony', $tag->getNom());
    }

    public function testCollectionArticlesVideeParDefaut(): void
    {
        $tag = new Tag();

        // La collection doit être initialisée vide à la construction
        $this->assertCount(0, $tag->getArticles());
    }

    public function testNomNull(): void
    {
        $tag = new Tag();

        // Sans setNom, le nom est null par défaut
        $this->assertNull($tag->getNom());
    }
}
