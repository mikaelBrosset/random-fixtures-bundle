<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 07/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;

abstract class Generator
{
    protected function selectRandom(array $data)
    {
        return $data[rand(0, count($data)-1)];
    }

    /**
     * Recursive function that makes sure the same key is not called more than onco
     */
    protected function getRandomKeyFromArray($data, &$nulledKeys) : void
    {
        $randomNb = rand(0, count($data)-1);
        if (array_key_exists($randomNb, $nulledKeys)) {
            $this->getRandomKeyFromArray($data, $nulledKeys);

        } else {
            $nulledKeys[$randomNb] = null;
        }
    }
}