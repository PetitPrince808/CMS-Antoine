<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testIndexRetourne200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
    }

    public function testSlugInexistantRetourne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/blog/article-qui-nexiste-pas');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentaireSansAuthRedirigeVersLogin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/blog/un-slug/commenter');

        // Sans authentification, #[IsGranted] doit rediriger vers /login
        $this->assertResponseRedirects('/login');
    }
}
