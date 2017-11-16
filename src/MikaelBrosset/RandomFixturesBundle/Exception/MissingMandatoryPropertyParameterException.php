<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class MissingMandatoryPropertyParameterException extends \Exception
{
    public function __construct($annotationPropertyName, $className, $property)
    {
        parent::__construct(sprintf("Mandatory @MBRF annotation property \"%s\" not found in %s::%s", $annotationPropertyName, $className, $property));
    }
}