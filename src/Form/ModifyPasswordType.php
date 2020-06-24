<?php

namespace App\Form;

use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifyPasswordType extends ConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'oldPassword',
                PasswordType::class,
                $this->getConfiguration(
                    'Ancien mot de passe',
                    'Veuillez saisir votre ancien mot de passe',
                    true,
                    'form-control'
                )
            )
            ->add(
                'newPassword',
                PasswordType::class,
                $this->getConfiguration(
                    'Nouveau mot de passe',
                    'Veuillez saisir votre nouveau mot de passe',
                    true,
                    'form-control'
                )
            )
            ->add(
                'confirmPassword',
                PasswordType::class,
                $this->getConfiguration(
                    'Cofirmation du mot de passe',
                    'Veuillez confirmer votre mot de passe',
                    true,
                    'form-control'
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
