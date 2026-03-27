<?php

// tests/Entity/ImageTest.php
namespace App\Tests\Entity;

use App\Entity\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testAddedAtInitialise(): void
    {
        $image = new Image();
        // La date d'ajout doit être définie automatiquement à la création
        $this->assertInstanceOf(\DateTimeInterface::class, $image->getAddedAt());
    }

    public function testUrl(): void
    {
        $image = new Image();
        $image->setUrl('uploads/galeries/photo.jpg');

        $this->assertSame('uploads/galeries/photo.jpg', $image->getUrl());
    }

    public function testLegendeNullable(): void
    {
        $image = new Image();
        // La légende est optionnelle
        $this->assertNull($image->getLegende());
    }
}
