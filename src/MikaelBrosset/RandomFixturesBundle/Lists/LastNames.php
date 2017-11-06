<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Lists;

class LastNames
{
    private $lastNames = [
        'Johnson', 'Nixon', 'Jordan', 'Jackson', 'Moore', 'Cobain', 'Hedger', 'Dos Santos', 'Frémion', 'Delaguila', 'Bernardo'
    ];

    public function getRandomLastName()
    {
        return $this->lastNames[rand(0, count($this->lastNames)-1)];
    }
}
