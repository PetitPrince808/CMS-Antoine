<?php

namespace App\Tests\Entity;

use App\Entity\Page;
use PHPUnit\Framework\TestCase;

class PageSlugTest extends TestCase
{
    public function testSlugGenereDepuisTitre(): void
    {
        $page = new Page();
        $page->setTitre('À propos de nous');
        $page->generateSlug();

        $this->assertSame('a-propos-de-nous', $page->getSlug());
    }

    public function testSlugManuelNonEcrase(): void
    {
        $page = new Page();
        $page->setTitre('À propos de nous');
        $page->setSlug('mon-slug-perso');
        $page->generateSlug();

        // Si un slug est déjà défini, il ne doit pas être remplacé
        $this->assertSame('mon-slug-perso', $page->getSlug());
    }

    public function testCaracteresSpeciauxNormalises(): void
    {
        $page = new Page();
        $page->setTitre('Ça & Là : "test"');
        $page->generateSlug();

        $this->assertSame('ca-la-test', $page->getSlug());
    }

    public function testTitreNullProduitsSlugNull(): void
    {
        $page = new Page();
        // Sans setTitre(), titre reste null — le slug doit aussi rester null
        $page->generateSlug();

        $this->assertNull($page->getSlug());
    }
}
