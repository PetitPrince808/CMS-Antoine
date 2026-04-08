<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testIndexRetourne200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pages');

        $this->assertResponseIsSuccessful();
    }

    public function testSlugInexistantRetourne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pages/slug-qui-nexiste-pas');

        $this->assertResponseStatusCodeSame(404);
    }
}
