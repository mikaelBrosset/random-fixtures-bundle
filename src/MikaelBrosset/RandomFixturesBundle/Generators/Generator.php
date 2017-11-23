<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 20/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\ResourceNotFoundException;

class Generator
{
    private $name = 'std';
    private $resourcePath;
    private $resourceName;
    private $value;
    private $requirements;
    protected $resourceList;

    public function setName($name) : Generator
    {
        $this->name = $name;
        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setResourcePath($path) : Generator
    {
        $this->resourcePath = $path;
        return $this;
    }

    public function getResourcePath() : string
    {
        return $this->resourcePath;
    }

    public function setResourceName($name) : Generator
    {
        $this->resourceName = $name;
        return $this;
    }

    public function getResourceName() : string
    {
        return $this->resourceName;
    }

    public function setResourceList($file) : Generator
    {
        $resource = __DIR__ . '/Resources/' . $file;

        $res = fopen($resource, 'r');
        $list = [];

        while ($ligne = fgetss($res)) {
            $list[] = $ligne;
        }
        fclose($res);
        $this->resourceList = $list;

        return $this;
    }

    public function setRequirement($generator) :  Generator
    {
        $this->requirements[] = $generator;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

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