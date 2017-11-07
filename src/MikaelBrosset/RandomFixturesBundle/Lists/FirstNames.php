<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 07/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Lists;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;

class FirstNames {

    public function getRandomFirstName(): string
    {
        $firstNames = array_merge($this->openFile('FemaleFirstNames'), $this->openFile('MaleFirstNames'));
        return $firstNames[rand(0, count($firstNames)-1)];
    }

    private function openFile(string $file): array
    {
        $resource = __DIR__ . '/Resources/' . $file;
        if (!is_readable($resource)) {
            new ListNotFoundException(__CLASS__);
        }

        $res = @fopen($resource, 'r');
        $list = [];
        while ($ligne = fgets($res)) {
            $list[] = $ligne;
        }

        return $list;
    }
}