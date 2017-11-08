<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;

class LastNameGenerator extends Generator implements GeneratorInterface
{
    public function getValue(): string
    {
        $lastnames = $this->openFile('lastnames');
        return $this->selectRandom($lastnames);
    }
}
