<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 15/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;

class ResourceRepository
{
    public function getNeededResources(array $resourceNames)
    {
        $r = [];
        foreach ($resourceNames as $name) {
            $r[] = $this->getResource($name);
        }
        return $r;
    }

    public function getResource(string $resourceName)
    {
        return $this->getResourceAsArray($resourceName);
    }

    public function getResourceAsArray(string $file): array
    {
        $resource = __DIR__ . '/Resources/' . $file;
        if (!is_readable($resource)) {
            new ListNotFoundException(__CLASS__);
        }
        $res = @fopen($resource, 'r');
        $list = [];
        while ($ligne = fgetss($res)) {
            $list[] = $ligne;
        }
        fclose($res);
        return $list;
    }
}