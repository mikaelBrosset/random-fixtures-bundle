<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 20/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\GeneratorNotFoundException;

class Group
{
    private $name;

    private $generators = [];

    public function setName($name) : Group
    {
        $this->name = $name;
        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setGenerators($generators) : Group
    {
        $this->name = $generators;
        return $this;
    }

    public function getGenerators() : array
    {
        return $this->generators;
    }

    public function addGenerator(Generator $generator) : Group
    {
        $this->generators[] = $generator;
        return $this;
    }

    public function removeGenerator(Generator $generator) : Group
    {
        if (!in_array($generator, $this->generators)) { throw new GeneratorNotFoundException($generator->getName(), $this->getName()); }
        unset($this->generators[array_search($generator, $this->generators)]);
        return $this;
    }
}