<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Modérer les commentaires est une responsabilité admin uniquement
#[IsGranted('ROLE_ADMIN')]
class CommentaireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commentaire::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextareaField::new('contenu', 'Contenu');
        yield DateTimeField::new('date', 'Date')->hideOnForm();
        yield ChoiceField::new('statut', 'Statut')->setChoices([
            'En attente' => 'en_attente',
            'Approuvé'   => 'approuve',
            'Rejeté'     => 'rejete',
        ]);
        yield AssociationField::new('article', 'Article');
        yield AssociationField::new('auteur', 'Auteur');
    }
}
