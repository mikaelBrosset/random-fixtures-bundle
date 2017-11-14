<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class MissingMandatoryParameterException extends \Exception
{
    public function __construct($propertyName, $className)
    {
        parent::__construct(sprintf("Mandatory annotation property \"%s\" not found in %s", $propertyName, $className));
    }
}