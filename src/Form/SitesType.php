<?php

namespace App\Form;

use App\Entity\Sites;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SitesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',
            TextType::class,
            [
                'label' => 'Nom du site',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sites::class,
        ]);
    }
}
