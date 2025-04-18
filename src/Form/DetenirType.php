<?php
namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Detenir;

class DetenirType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un ingrédient',
                'query_builder' => function (\App\Repository\IngredientRepository $repo) {
                    return $repo->createQueryBuilder('i')
                        ->orderBy('i.nom', 'ASC');
                },
                'attr' => ['class' => 'form-control w-25'],
                'label' => 'Nom de l\'ingrédient',

            ])
            ->add('quantite', null, [
                'attr' => ['class' => 'form-control w-25'],
                'label' => 'Quantité',
            ])
            ->add('unite', null, [
                'attr' => ['class' => 'form-control w-25'],
                'label' => 'Unité',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Detenir::class,
        ]);
    }
}