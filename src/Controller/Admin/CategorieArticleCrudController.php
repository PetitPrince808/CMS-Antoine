<?php

namespace App\Controller\Admin;

use App\Entity\CategorieArticle;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_REDACTEUR')]
class CategorieArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategorieArticle::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('nom', 'Nom');
    }
}
