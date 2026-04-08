<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_REDACTEUR')]
class PageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titre', 'Titre');
        yield TextEditorField::new('paragraphes', 'Contenu')->hideOnIndex();
        yield TextField::new('slug', 'Slug (URL)');
        yield ChoiceField::new('statut', 'Statut')->setChoices([
            'Brouillon' => 'brouillon',
            'Publiée'   => 'publie',
            'Archivée'  => 'archive',
        ]);
        yield AssociationField::new('categoriePage', 'Catégorie');
        // Relation récursive : permet de sélectionner une page parente
        yield AssociationField::new('parent', 'Page parente');
        yield AssociationField::new('galerie', 'Galerie associée');
        yield TextareaField::new('metaDescription', 'Méta-description')->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Créée le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Modifiée le')->hideOnForm();
    }
}
