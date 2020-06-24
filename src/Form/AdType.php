<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration("Titre de l'annonce", 'Saisissez votre titre')
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration("Brève présentation", 'Petite phrase d\'introduction')
            )
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfiguration("Image de couverture", 'Url de l\'image de couverture')
            )
            ->add(
                'content',
                TextType::class,
                $this->getConfiguration("Contenu de l'annonce", 'Saisissez un contenu complet pour l\'annonce')
            )
            ->add(
                'price',
                MoneyType::class,
                $this->getConfiguration("Prix de la location / nuit", 'Saisissez le prix par nuit')
            )
            ->add(
                'rooms',
                IntegerType::class,
                $this->getConfiguration("Nombre de chambres à proposer", 'De combien de chambres disposez-vous ?')
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    'label'=>"Images complémentaires",
                    'label_attr'=>["class"=>"font-weight-bold border-top my-4 border-grey"],
                    'entry_type'=> ImageType::class,
                    'entry_options'=>['label'=>false],
                    'allow_add'=> true,
                    'allow_delete' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
