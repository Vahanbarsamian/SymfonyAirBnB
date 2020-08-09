<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ConfigurationType extends AbstractType
{
    /**
    * This function returns the label and the placeholder for each field
    *
    * @param string $label
    * @param string $placeholder
    * @return array
    */
    protected function getConfiguration(string $label, string $placeholder, $required = true, $class = null) :array
    {
        return [
            'label'=>\utf8_encode($label),
            'attr'=>[
                'placeholder'=>\utf8_encode($placeholder),
                'class'=>$class
            ],
            'required'=>$required
        ];
    }
}
