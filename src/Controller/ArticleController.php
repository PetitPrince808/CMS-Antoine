<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Service\CommentaireHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArticleController extends AbstractController
{
    /**
     * Liste tous les articles publiés.
     */
    #[Route('/blog', name: 'app_blog_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'articles' => $articleRepository->findPublished(),
        ]);
    }

    /**
     * Affiche un article publié avec ses commentaires approuvés et le formulaire de commentaire.
     */
    #[Route('/blog/{slug}', name: 'app_blog_show', methods: ['GET'])]
    public function show(string $slug, ArticleRepository $articleRepository, CommentaireRepository $commentaireRepository): Response
    {
        $article = $articleRepository->findOnePublishedBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException("Article introuvable : $slug");
        }

        // Requête SQL directe — évite de charger tous les commentaires en mémoire
        $commentaires = $commentaireRepository->findApprovedByArticle($article);

        // Formulaire instancié uniquement pour les utilisateurs connectés
        $form = $this->isGranted('IS_AUTHENTICATED_FULLY')
            ? $this->createForm(CommentaireType::class, new Commentaire())
            : null;

        return $this->render('blog/show.html.twig', [
            'article'      => $article,
            'commentaires' => $commentaires,
            'form'         => $form,
        ]);
    }

    /**
     * Reçoit la soumission du formulaire de commentaire.
     * Réservé aux utilisateurs connectés (#[IsGranted]).
     */
    #[Route('/blog/{slug}/commenter', name: 'app_blog_comment', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function comment(
        string $slug,
        Request $request,
        ArticleRepository $articleRepository,
        CommentaireHandler $handler,
    ): Response {
        $article = $articleRepository->findOnePublishedBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException("Article introuvable : $slug");
        }

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $handler->submit($commentaire, $article, $user);
            $this->addFlash('success', 'Votre commentaire a été soumis et attend modération.');
        }

        return $this->redirectToRoute('app_blog_show', ['slug' => $slug]);
    }
}
