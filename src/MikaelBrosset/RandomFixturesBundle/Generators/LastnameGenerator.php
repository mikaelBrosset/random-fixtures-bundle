<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;

class LastnameGenerator extends Generator implements GeneratorInterface
{
    public function getValue($null = 0, $option = null): string
    {
        $lastnames = $this->openFile('lastnames');
        return $this->selectRandom($lastnames);
    }
}
