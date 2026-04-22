<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    /**
     * Page d'accueil — affiche les pages racines et les derniers articles du blog.
     */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(PageRepository $pageRepository, ArticleRepository $articleRepository): Response
    {
        $pages = $pageRepository->findPublishedRoots();
        $latestArticles = $articleRepository->findLatest(3);

        return $this->render('page/index.html.twig', [
            'pages' => $pages,
            'latestArticles' => $latestArticles,
        ]);
    }

    /**
     * Liste toutes les pages racines publiées.
     */
    #[Route('/pages', name: 'app_page_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        $pages = $pageRepository->findPublishedRoots();

        return $this->render('page/index.html.twig', [
            'pages' => $pages,
        ]);
    }

    /**
     * Affiche une page par son slug.
     * Retourne 404 si la page n'existe pas ou n'est pas publiée.
     */
    #[Route('/pages/{slug}', name: 'app_page_show', methods: ['GET'])]
    public function show(string $slug, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOnePublishedBySlug($slug);

        if (!$page) {
            throw $this->createNotFoundException("Page introuvable : $slug");
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }
}
