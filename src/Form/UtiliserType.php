<?php
namespace App\Form;

use App\Entity\Ustensile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Utiliser;

class UtiliserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ustensile', EntityType::class, [
                'class' => Ustensile::class,
                'choice_label' => 'description',
                'placeholder' => 'Sélectionnez un ustensile',
                'query_builder' => function (\App\Repository\UstensileRepository $repo) {
                    return $repo->createQueryBuilder('u')
                        ->orderBy('u.description', 'ASC');
                },
                'attr' => ['class' => 'form-control w-25'],
                'label' => 'Nom de l\'ustensile',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utiliser::class,
        ]);
    }
}