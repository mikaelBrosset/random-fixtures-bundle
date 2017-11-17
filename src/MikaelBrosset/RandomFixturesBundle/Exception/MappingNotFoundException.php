<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class MappingNotFoundException extends \Exception
{
    public function __construct($generator)
    {
        parent::__construct(sprintf("Mandatory generator mapping not found for %s in config file", $generator));
    }
}