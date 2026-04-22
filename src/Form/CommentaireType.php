<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire de soumission d'un commentaire — champ contenu avec validation minimale.
 */
class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('contenu', TextareaType::class, [
            'label' => 'Votre commentaire',
            'attr'  => ['rows' => 4, 'class' => 'form-control'],
            'constraints' => [
                new NotBlank(message: 'Le commentaire ne peut pas être vide.'),
                new Length(
                    min: 5,
                    minMessage: 'Le commentaire doit contenir au moins {{ limit }} caractères.'
                ),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Commentaire::class]);
    }
}
