<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class GeneratorNotFoundException extends \Exception
{
    public function __construct($generatorName, $groupName)
    {
        parent::__construct(sprintf("Generator %s not added in Group %s", $generatorName, $groupName));
    }
}