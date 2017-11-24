<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class WrongCharacterNumberException extends \Exception
{
    public function __construct($character, $options, $name, $nb = 1)
    {
        parent::__construct(sprintf("Too many \"%s\" characters in options \"%s\" for %s. Only %d allowed !", $character, $options, $name, $nb));
    }
}