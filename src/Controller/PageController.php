<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    /**
     * Page d'accueil — affiche la liste des pages publiées.
     */
    #[Route('/', name: 'app_home')]
    public function home(PageRepository $pageRepository): Response
    {
        $pages = $pageRepository->findPublishedRoots();

        return $this->render('page/index.html.twig', [
            'pages' => $pages,
        ]);
    }

    /**
     * Liste toutes les pages racines publiées.
     */
    #[Route('/pages', name: 'app_page_index')]
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
    #[Route('/pages/{slug}', name: 'app_page_show')]
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
