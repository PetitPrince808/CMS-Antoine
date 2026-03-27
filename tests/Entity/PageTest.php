<?php

namespace App\Tests\Entity;

use App\Entity\Page;
use App\Entity\CategoriePage;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testStatutParDefaut(): void
    {
        $page = new Page();
        $this->assertSame('brouillon', $page->getStatut());
    }

    public function testDatesInitialisees(): void
    {
        $page = new Page();
        // createdAt et updatedAt doivent être définis à la création
        $this->assertInstanceOf(\DateTimeInterface::class, $page->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $page->getUpdatedAt());
    }

    public function testRelationParent(): void
    {
        $parent = new Page();
        $parent->setTitre('Page parente');

        $enfant = new Page();
        $enfant->setParent($parent);

        $this->assertSame($parent, $enfant->getParent());
    }

    public function testCategorie(): void
    {
        $page = new Page();
        $categorie = new CategoriePage();
        $categorie->setNom('Institutionnel');

        $page->setCategoriePage($categorie);

        $this->assertSame('Institutionnel', $page->getCategoriePage()->getNom());
    }
}
