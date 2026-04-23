<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_REDACTEUR')]
class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titre', 'Titre');
        yield TextEditorField::new('contenu', 'Contenu')->hideOnIndex();
        yield ChoiceField::new('statut', 'Statut')->setChoices([
            'Brouillon' => 'brouillon',
            'Publié'    => 'publie',
            'Archivé'   => 'archive',
        ]);
        yield AssociationField::new('categorieArticle', 'Catégorie');
        yield AssociationField::new('tags', 'Tags');
        yield AssociationField::new('auteur', 'Auteur');
        yield DateTimeField::new('datePublication', 'Date de publication');
        yield TextareaField::new('metaDescription', 'Méta-description')->hideOnIndex();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('statut', 'Statut')->setChoices([
                'Brouillon' => 'brouillon',
                'Publié'    => 'publie',
                'Archivé'   => 'archive',
            ]))
            ->add(EntityFilter::new('categorieArticle', 'Catégorie'))
            ->add(EntityFilter::new('tags', 'Tags'))
            ->add(EntityFilter::new('auteur', 'Auteur'))
            ->add(DateTimeFilter::new('datePublication', 'Date de publication'));
    }
}
