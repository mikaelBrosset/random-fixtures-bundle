<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class PropertyNotFoundException extends \Exception
{
    public function __construct($propertyName, $className)
    {
        parent::__construct(sprintf("Mandatory public property \"%s\" not found in %s", $propertyName, $className));
    }
}