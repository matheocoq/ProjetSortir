<?php

namespace App\Form;

use App\Entity\Ville;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',
                TextType::class,
                [
                'label' => 'Nom de la ville',
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
            ->add('code_postal',
                TextType::class,
                [
                'label' => 'Code postal',
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuiller entrer un code postal'
                    ]),
                    new Regex('/^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$/'),
                    new Length([
                        'min' => 3,
                        'minMessage' => '3 characters min',
                        'max' => 5,
                        'maxMessage' => '5 characters max',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
