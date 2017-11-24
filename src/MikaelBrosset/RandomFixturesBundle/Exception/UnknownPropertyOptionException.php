<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class UnknownPropertyOptionException extends \Exception
{
    public function __construct($name, $option, $availableOptions)
    {
        if (is_null($availableOptions)) {
            parent::__construct(sprintf("Option \"%s\" unavailable for \"%s\". No options available for this parameter!", $option, $name));
        } else {
            parent::__construct(sprintf("Option \"%s\" unavailable for \"%s\". Available options are : \"%s\"", $option, $name, implode(',', $availableOptions)));
        }
    }
}