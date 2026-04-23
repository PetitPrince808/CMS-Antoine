<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
            ->setTitle('NEXO')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home'),
            MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'app_home'),

            MenuItem::section('Blog'),
            MenuItem::linkTo(ArticleCrudController::class, 'Articles', 'fa fa-newspaper'),
            MenuItem::linkTo(CategorieArticleCrudController::class, 'Catégories', 'fa fa-tags'),
            MenuItem::linkTo(TagCrudController::class, 'Tags', 'fa fa-tag'),
            MenuItem::linkTo(CommentaireCrudController::class, 'Commentaires', 'fa fa-comments')
                ->setPermission('ROLE_ADMIN'),

            MenuItem::section('Pages'),
            MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa fa-file-alt'),
            MenuItem::linkTo(CategoriePageCrudController::class, 'Catégories de pages', 'fa fa-folder'),

            MenuItem::section('Médias'),
            MenuItem::linkTo(GalerieCrudController::class, 'Galeries', 'fa fa-images'),
            MenuItem::linkTo(CategorieGalerieCrudController::class, 'Catégories de galeries', 'fa fa-folder-open'),
            MenuItem::linkTo(ImageCrudController::class, 'Images', 'fa fa-image'),

            MenuItem::section('Administration')->setPermission('ROLE_ADMIN'),
            MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users')->setPermission('ROLE_ADMIN'),
        ];
    }
}
