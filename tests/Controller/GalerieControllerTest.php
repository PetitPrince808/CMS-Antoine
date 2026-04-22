<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GalerieControllerTest extends WebTestCase
{
    public function testIndexRetourne200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/galeries');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nos Galeries');
    }

    public function testGalerieInexistanteRetourne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/galeries/999');

        $this->assertResponseStatusCodeSame(404);
    }
}
