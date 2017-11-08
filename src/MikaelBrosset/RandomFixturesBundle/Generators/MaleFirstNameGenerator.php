<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;

class MaleFirstNameGenerator
{
    public function getValue(): string
    {
        $maleFirstnames = $this->openFile('male-firstnames');
        return $this->selectRandom($maleFirstnames);
    }
}
