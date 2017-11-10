<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

class FemaleFirstNameGenerator extends Generator implements GeneratorInterface
{

    public function getValue($null = 0, $option = null): string
    {
        $femaleFirstnames = $this->openFile('female-firstnames');
        return $this->selectRandom($femaleFirstnames);
    }
}