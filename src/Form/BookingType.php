<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'startDate',
                DateType::class,
                array_merge(
                    $this->getConfiguration(
                        "Date d'arrivée souhaitée",
                        "Veuillez renseigner votre date d'arivée ici",
                        true,
                        null,
                    ),
                    [ "widget" => "single_text"]
                )
            )
            ->add(
                'endDate',
                DateType::class,
                array_merge(
                    $this->getConfiguration(
                        "Date de départ estimée",
                        "Veuillez renseigner votre date de départ",
                        true,
                        null
                    ),
                    [ "widget" => "single_text"]
                )
            )
            ->add(
                "comment",
                TextareaType::class,
                $this->getConfiguration(
                    "Un commentaire ?",
                    "Merci de déposer ici votre commentaire...Si vous le désirez",
                    false
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
