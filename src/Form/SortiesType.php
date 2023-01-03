<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sorties;
use PHPUnit\Framework\Constraint\LessThan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Nom de la sortie',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control mb-2'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a nom'
                        ]),
                        new Length([
                            'min' => 3,
                            'minMessage' => '3 characters min'
                        ])
                    ]
                ]
            )
            ->add(
                'date_debut',
                DateTimeType::class,
                [
                    'label' => 'Date de début',
                    'html5' => true,
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control mb-2'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a date début'
                        ]),
                        new GreaterThan([
                            'value' => new \DateTime()
                        ])
                    ]
                ]
            )
            ->add(
                'duree',
                null, [
                'label' => 'Durée (min)',
                'attr' => [
                    'class' => 'form-control mb-2',
                    'min' => '0'
                ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a duree'
                        ]),
                        new GreaterThan([
                            'value' => 0
                        ])
                    ]
            ])
            ->add(
                'date_cloture',
                DateTimeType::class,
                [
                    'label' => 'Date de cloture',
                    'html5' => true,
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control mb-2'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a date cloture'
                        ]),
                        new GreaterThan([
                            'value' => new \DateTime()
                        ])
                    ]
                ]
            )
            ->add(
                'nb_inscription_max',
                null, [
                'label' => 'Nombre maximum de participant',
                'attr' => [
                    'type' => 'number',
                    'class' => 'form-control mb-2',
                    'min' => '0'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a nb_inscription_max'
                    ]),
                    new GreaterThan([
                        'value' => 0
                    ])
                ]
            ])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description de la sortie',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control mb-2'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a description'
                        ])
                    ]
                ]
            )
            ->add(
                'lieux',
                EntityType::class, [
                'label' => 'Lieux',
                'class' => Lieux::class,
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
            'data_class' => Sorties::class,
        ]);
    }
}
