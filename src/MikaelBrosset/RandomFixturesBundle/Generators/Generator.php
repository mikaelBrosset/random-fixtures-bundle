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
    protected function selectRandom(array $data): string
    {
        return $data[rand(0, count($data)-1)];
    }

    protected function openFile(string $file): array
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