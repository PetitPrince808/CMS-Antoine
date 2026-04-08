<?php

namespace App\Controller\Admin;

use App\Entity\CategoriePage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_REDACTEUR')]
class CategoriePageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoriePage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('nom', 'Nom');
    }
}
