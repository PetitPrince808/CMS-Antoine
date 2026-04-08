<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\CategorieArticle;
use App\Entity\CategoriePage;
use App\Entity\Commentaire;
use App\Entity\Galerie;
use App\Entity\Image;
use App\Entity\Page;
use App\Entity\Tag;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Seuls les rédacteurs et admins peuvent accéder au back-office
#[IsGranted('ROLE_REDACTEUR')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CMS DISII')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Articles', 'fa fa-newspaper', Article::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', CategorieArticle::class);
        yield MenuItem::linkToCrud('Tags', 'fa fa-tag', Tag::class);
        // Modération des commentaires — réservée aux admins
        yield MenuItem::linkToCrud('Commentaires', 'fa fa-comments', Commentaire::class)
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Pages');
        yield MenuItem::linkToCrud('Pages', 'fa fa-file-alt', Page::class);
        yield MenuItem::linkToCrud('Catégories de pages', 'fa fa-folder', CategoriePage::class);

        yield MenuItem::section('Médias');
        yield MenuItem::linkToCrud('Galeries', 'fa fa-images', Galerie::class);
        yield MenuItem::linkToCrud('Images', 'fa fa-image', Image::class);

        // Gestion des utilisateurs réservée aux admins
        yield MenuItem::section('Administration')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class)
            ->setPermission('ROLE_ADMIN');
    }
}
