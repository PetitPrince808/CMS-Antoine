<?php

// tests/Entity/GalerieTest.php
namespace App\Tests\Entity;

use App\Entity\Galerie;
use App\Entity\Image;
use PHPUnit\Framework\TestCase;

class GalerieTest extends TestCase
{
    public function testAjoutImage(): void
    {
        $galerie = new Galerie();
        $image = new Image();

        $galerie->addImage($image);

        // addImage doit maintenir la relation bidirectionnelle
        $this->assertCount(1, $galerie->getImages());
        $this->assertSame($galerie, $image->getGalerie());
    }

    public function testSuppressionImage(): void
    {
        $galerie = new Galerie();
        $image = new Image();

        $galerie->addImage($image);
        $galerie->removeImage($image);

        // removeImage doit détacher l'image de la galerie
        $this->assertCount(0, $galerie->getImages());
        $this->assertNull($image->getGalerie());
    }

    public function testNom(): void
    {
        $galerie = new Galerie();
        $galerie->setNom('Galerie printemps');

        $this->assertSame('Galerie printemps', $galerie->getNom());
    }
}
