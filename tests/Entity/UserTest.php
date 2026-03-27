<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_REDACTEUR']);

        // ROLE_USER est toujours ajouté automatiquement par Symfony
        $this->assertContains('ROLE_REDACTEUR', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getUserIdentifier());
    }

    public function testNom(): void
    {
        $user = new User();
        $user->setNom('Antoine');

        $this->assertSame('Antoine', $user->getNom());
    }
}
