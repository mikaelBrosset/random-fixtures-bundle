<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class WrongOptionFormatException extends \Exception
{
    public function __construct($character, $options, $name)
    {
        parent::__construct(sprintf("Wrong format for options \"%s\" for %s. Format has to be for example \"@MBRFProp( type=\"text\", nullable=50, options=\"length=50, upper=true\")\"",
            $character, $options, $name));
    }
}