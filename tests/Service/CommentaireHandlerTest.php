<?php

namespace App\Tests\Service;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\User;
use App\Service\CommentaireHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CommentaireHandlerTest extends TestCase
{
    public function testSubmitAssocieAuteurArticleEtStatut(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $handler = new CommentaireHandler($em);

        $article = new Article();
        $auteur = new User();
        $commentaire = new Commentaire();
        $commentaire->setContenu('Super article !');

        $handler->submit($commentaire, $article, $auteur);

        $this->assertSame($article, $commentaire->getArticle());
        $this->assertSame($auteur, $commentaire->getAuteur());
        $this->assertSame('en_attente', $commentaire->getStatut());
    }
}
