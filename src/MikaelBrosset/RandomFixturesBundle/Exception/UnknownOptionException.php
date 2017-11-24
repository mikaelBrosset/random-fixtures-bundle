<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class UnknownOptionException extends \Exception
{
    public function __construct($option)
    {
        parent::__construct(sprintf("Unknown option \"%s\"", $option));
    }
}