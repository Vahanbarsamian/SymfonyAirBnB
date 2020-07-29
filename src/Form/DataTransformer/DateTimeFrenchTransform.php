<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeFrenchTransform implements DataTransformerInterface
{
    /**
     * This method return a French format Date
     *
     * @param DateTime $date
     * @return mixed
     * @throws TransformationFailedException when the transformation fails
     */
    public function transform($date)
    {
        if ($date === null) {
            return '';
        }
        return $date->format("d/m/Y");
    }

    /**
     * This method construct a real dateTime object from string
     *
     * @param mixed $value
     * @return mixed
     * @throws TransformationFailedException when the transformation fails
     */
    public function reverseTransform($value)
    {
        if (null ===  $value) {
            throw new TransformationFailedException("Vous devez fournir une date valide !!!");
        }

        $date = \DateTime::createFromFormat('d/m/Y', $value);

        if ($date===false) {
            throw new TransformationFailedException("Vous devez fournir une date valide !!!");
        }
        
        return $date;
    }
}
