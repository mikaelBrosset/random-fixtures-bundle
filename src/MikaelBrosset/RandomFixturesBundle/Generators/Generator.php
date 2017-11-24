<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 20/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\OutOfRangeException;
use MikaelBrosset\RandomFixturesBundle\Exception\ResourceNotFoundException;

class Generator
{
    protected $name = 'std';
    protected $resourcePath;
    protected $resourceName = null;
    protected $resourceList;
    protected $availableOptions = null;
    private   $value;

    public function setName($name) : Generator
    {
        $this->name = $name;
        return $this;
    }

    public function setResourcePath($path) : Generator
    {
        $this->resourcePath = $path;
        return $this;
    }

    public function setResourceName($name) : Generator
    {
        $this->resourceName = $name;
        return $this;
    }

    public function openAndSetResourceList($file) : array
    {
        $resource = __DIR__ . '/Resources/' . $file;

        $res = fopen($resource, 'r');
        $list = [];

        while ($ligne = fgetss($res)) {
            $list[] = $ligne;
        }
        fclose($res);
        $this->resourceList = $list;

        return $list;
    }

    public function setResourceList($list)
    {
        $this->resourceList = $list;
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

    public function setSomeListElementsAsNull(int $nullable)
    {
        var_dump($nullable);
        if ($nullable < 0 || $nullable >100) {
            throw new OutOfRangeException(0, 100, $this->name);
        }

        // If null is set to 0 or 100, that's easy, so we cut the process
        if ($nullable === 0) {return;
        } elseif ($nullable === 100) {
            foreach ($this->resourceList as $key => $value) {
                $this->resourceList[$key] = null;
            }
        }

        $actualNullables = (int) round(count($this->resourceList) / 100 * $nullable);
        $nulledKeys = [];

        for ($i=0; $i<$actualNullables; $i++) {
            $this->getRandomKeyFromArray($this->resourceList, $nulledKeys);
        }

        foreach ($nulledKeys as $key => $value) {
            $this->resourceList[$key] = null;
        }
    }

    public function setAvailableOptions(array $options) : Generator
    {
        $this->availableOptions = $options;
        return $this;
    }


    protected function chunkOptions(string $optionsString) : array
    {
        $o = [];
        $options = explode(',', $optionsString);
        for ($i=0; $i<count($options); $i++) {

            if (substr_count($options[$i], '=') === 0) {
                throw new WrongOptionFormatException('=', $options[$i], $this->getName());
            }
            if (substr_count($options[$i], '=') > 1) {
                throw new WrongCharacterNumberException('=', $options[$i], $this->getName());
            }
            list($key, $value) = explode('=', $options[$i]);
            $o[trim($key)] = trim($value);
        }
        return $o;
    }

    /**
     * Checks declared annotation options in entity are available for this property
     */
    protected function checkOptionsExists(array $options)
    {
        foreach ($options as $o => $val) {
            if (!defined("MikaelBrosset\\RandomFixturesBundle\\Annotation\\MBRFOptions::$o")) {
                throw new UnknownPropertyOptionException($this->name, $o, $this->availableOptions);
            }
        }
    }
}