<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Lists;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;

class FemaleFirstNames {

    public function getRandomFemaleFirstName(): string
    {
        $data = $this->openFile();
        return $data[rand(0, count($data)-1)];
    }

    private function openFile(): array
    {
        $resource = __DIR__ . '/Resources/FemaleFirstNames';
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