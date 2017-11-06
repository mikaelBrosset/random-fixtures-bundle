<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Lists;

class MaleFirstNames
{
    private $maleFirstNames = [
        'Jon', 'John', 'Dave', 'David', 'Edgar', 'Edmund', 'Albert', 'Samuel', 'Jonathan', 'Clement', 'Marc', 'Carl', 'Emmanuel', 'François', 'Igor'
    ];

    public function __construct()
    {
        return $this;
    }

    public function getRandomMaleFirstName()
    {
        return $this->maleFirstNames[rand(0, count($this->maleFirstNames)-1)];
    }
}
