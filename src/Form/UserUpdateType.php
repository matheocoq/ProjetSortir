<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',null,['attr' => [
        'class' => 'form-control mb-1'
    ]])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
