<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends ConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                $this->getConfiguration('Prénom', "Votre prénom ici", true, 'form-control')
            )
            ->add(
                'lastName',
                TextType::class,
                $this->getConfiguration('Nom', "Votre nom ici", true, 'form-control')
            )
            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration('Email', "Votre email ici", true, 'form-control')
            )
            ->add(
                'picture',
                UrlType::class,
                $this->getConfiguration('Photo de profil', "Url de l'image", false, 'form-control')
            )
            ->add(
                'hash',
                PasswordType::class,
                $this->getConfiguration('Mot de passe', "Votre mot de passe", true, 'form-control')
            )
            ->add(
                'confirmPassword',
                PasswordType::class,
                $this->getConfiguration('Confirmation du mot de passe', "Confirmez votre mot de passe", true, 'form-control')
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration('Introduction', "Petite introduction ici", true, 'form-control')
            )
            ->add(
                'description',
                TextareaType::class,
                $this->getConfiguration('Description', "Texte de presentation", true, 'form-control')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
