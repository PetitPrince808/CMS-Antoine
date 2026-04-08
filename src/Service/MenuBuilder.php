<?php

namespace App\Service;

use App\Repository\PageRepository;

/**
 * Fournit les données de navigation communes à tous les templates.
 *
 * Centralisé ici pour éviter de répéter la requête dans chaque contrôleur.
 */
class MenuBuilder
{
    public function __construct(private readonly PageRepository $pageRepository)
    {
    }

    /**
     * Retourne les pages racines publiées pour construire le menu principal.
     *
     * @return \App\Entity\Page[]
     */
    public function getMenuPages(): array
    {
        return $this->pageRepository->findPublishedRoots();
    }
}
