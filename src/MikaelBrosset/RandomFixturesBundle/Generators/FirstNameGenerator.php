<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 07/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

class FirstNameGenerator extends Generator {

    public function getRandomFirstName(): string
    {
        $firstnames = array_merge($this->openFile('female-firstnames'), $this->openFile('male-firstnames'));
        return $this->selectRandom($firstnames);
    }


}