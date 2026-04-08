<?php

namespace App\Tests\Entity;

use App\Entity\CategoriePage;
use App\Entity\Page;
use PHPUnit\Framework\TestCase;

class CategoriePageTest extends TestCase
{
    public function testNom(): void
    {
        $categorie = new CategoriePage();
        $categorie->setNom('Institutionnel');

        $this->assertSame('Institutionnel', $categorie->getNom());
    }

    public function testCollectionPagesVideeParDefaut(): void
    {
        $categorie = new CategoriePage();

        $this->assertCount(0, $categorie->getPages());
    }

    public function testAddPage(): void
    {
        $categorie = new CategoriePage();
        $page = new Page();
        $page->setTitre('À propos');

        $categorie->addPage($page);

        // addPage() doit synchroniser la relation inverse
        $this->assertCount(1, $categorie->getPages());
        $this->assertSame($categorie, $page->getCategoriePage());
    }

    public function testRemovePage(): void
    {
        $categorie = new CategoriePage();
        $page = new Page();
        $page->setTitre('Contact');

        $categorie->addPage($page);
        $categorie->removePage($page);

        $this->assertCount(0, $categorie->getPages());
        $this->assertNull($page->getCategoriePage());
    }
}
