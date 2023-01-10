<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Ville;
use Symfony\Component\Form\Extension\Core\Type\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LieuxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',
            TextType::class,
            [
                'label' => 'Nom du lieu',
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuiller entrer un nom'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => '3 characters min'
                    ])
                ]
            ])
            ->add('rue',
            TextType::class,
            [
                'required' => false,
                'label' => 'Rue du lieu',
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
            ])
            ->add('latitude',
            NumberType::class,
            [
                'required' => false,
                'label' => 'Latitude du lieu',
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
            ])
            ->add('longitude',
            NumberType::class,
            [
                'required' => false,
                'label' => 'Longitude du lieu',
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
            ])
            ->add('ville',
                EntityType::class, [
                'label' => 'Ville',
                'class' => Ville::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieux::class,
        ]);
    }
}
