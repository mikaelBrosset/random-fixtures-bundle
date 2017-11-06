<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Lists;

class FemaleFirstNames {
    private $femaleFirstNames = [
        'Claire', 'Alexandra', 'Rebecca', 'Sophie', 'Michelle', 'Melania', 'Cecilia', 'Emilia', 'Elsa', 'Salomé'
    ];

    public function getRandomFemaleFirstName()
    {
        return $this->femaleFirstNames[rand(0, count($this->femaleFirstNames)-1)];
    }

    private function openFile()
    {
        if
    }
}