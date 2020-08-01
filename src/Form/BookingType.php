<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use App\Form\DataTransformer\DateTimeFrenchTransform;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ConfigurationType
{

    private $transform;
    
    public function __construct(DateTimeFrenchTransform $transform)
    {
        $this->transform = $transform;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'startDate',
                TextType::class,
                array_merge(
                    $this->getConfiguration(
                        \utf8_decode("Date d'arrivée souhaitée"),
                        "jj/mm/aaaa",
                        true,
                        null,
                    )
                )
            )
            ->add(
                'endDate',
                TextType::class,
                array_merge(
                    $this->getConfiguration(
                        \utf8_decode("Date de départ estimée"),
                        "jj/mm/aaaa",
                        true,
                        null
                    )
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
        $builder-> get('startDate')->addModelTransformer($this->transform);
        $builder-> get('endDate')->addModelTransformer($this->transform);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
