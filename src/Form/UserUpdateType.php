<?php

namespace App\Form;

use App\Entity\Sites;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',
                EmailType::class,[
                    'constraints' => [
                        new NotBlank(),
                        new Regex('^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$^')
                    ],
                    'attr' => [
                        'class' => 'form-control mb-1'
                    ]
                ])
            ->add('password',RepeatedType::class, [
        'type' => PasswordType::class,
        'invalid_message' => 'Les deux mots de passes doivent correspondre.',
        'options' => ['attr' => ['class' => 'form-control mb-1']],
        'required' => true,
        'first_options'  => ['label' => 'Mot de passe'],
        'second_options' => ['label' => 'Répeter le mot de passe'],
        ])
            ->add('nom',null,['attr' => [
                'class' => 'form-control mb-1'
            ]])
            ->add('prenom',null,['attr' => [
                'class' => 'form-control mb-1'
            ]])
            ->add('pseudo',null,['attr' => [
                'class' => 'form-control mb-1'
            ]])
            ->add('telephone',null,[ 'constraints' => [
                new NotBlank(),
                new Regex('^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$^')
            ],'attr' => [
                'class' => 'form-control mb-1'
            ]]) 
            ->add(
                'sites',
                EntityType::class, [
                    'label' => 'Site',
                    'class' => Sites::class,
                    'choice_label' => 'nom',
                    'attr' => [
                        'class' => 'form-control mb-2'
                    ]
                ])
            ->add('image', FileType::class, [
                'label' => 'Ma photo',
                'attr' => [
                    'class' => 'form-control mb-1'
                ],
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid picture document',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
