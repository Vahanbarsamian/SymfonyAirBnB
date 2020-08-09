<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends ConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'rating',
                IntegerType::class,
                array_merge_recursive(
                    $this->getConfiguration(
                        \utf8_decode('Votre note sur 5 (1 : déconseillé... 5 : extra)'),
                        \utf8_decode("Saisissez une note de 1 à 5"),
                    ),
                    ['attr'=>
                    ['min'=>1,
                    'max'=>5,
                    'step'=>1]]
                )
            )
                
            ->add(
                'content',
                TextareaType::class,
                $this->getConfiguration(
                    \utf8_decode("Ce que vous avez aimé / pas aimé"),
                    \utf8_decode("Décrivez ici les points forts et/ou les points faibles")
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
