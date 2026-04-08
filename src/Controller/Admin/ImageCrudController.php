<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_REDACTEUR')]
class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        // Affichage de l'image via son chemin relatif depuis /public
        yield ImageField::new('url', 'Image')
            ->setBasePath('/')
            ->setUploadDir('public/uploads/galeries')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]');
        yield TextField::new('legende', 'Légende');
        yield AssociationField::new('galerie', 'Galerie');
        yield DateTimeField::new('addedAt', 'Ajoutée le')->hideOnForm();
    }
}
