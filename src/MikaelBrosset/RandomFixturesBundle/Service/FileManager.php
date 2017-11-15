<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 08/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use MikaelBrosset\RandomFixturesBundle\Exception\MethodNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\NamespaceNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\PropertyNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class FileManager extends AbstractManager
{
    protected $ymlConfig;

    /**
     * Find php Files in Entity folder and subfolders
     */
    protected function findFiles() : \RegexIterator
    {
        $f = new Finder();
        $f->files()
            ->in($this->absDir);

        $it = $f->files()->getIterator();
        $rit = new \RegexIterator($it, "#Entity\/\w+\/*\.php$#");

        if (is_null(!$rit->valid())) {
            $this->output->writeln(sprintf("<error>No php Entity found in %s<error>", $this->absDir));
            exit();
        }
        return $rit;
    }

    /**
     * Maps the annotation name with their Generator
     */
    function parseYmlConfig()
    {
        $this->ymlConfig = Yaml::parse(file_get_contents($this->absDir . 'MikaelBrosset/RandomFixturesBundle/Config/config.yml'));
    }

    /**
     * Turns a pathname into a namespace according to PSR-4
     */
    protected function loadClassFromNamespace(SplFileInfo $file)
    {
        $namespace = str_replace('/', '\\', substr($file->getRelativePathname(), 0, strpos($file->getRelativePathname(), '.php')));
        if (!is_object($c = new $namespace)) {
            throw new NamespaceNotFoundException($c);
        }
        return $c;
    }

    function validatePropertiesAndSetters() : void
    {
        $ymlMBRFClass = array_keys($this->ymlConfig['MBRFClass']);
        $ymlMBRFProp  = array_keys($this->ymlConfig['MBRFProp']);

        foreach ($ymlMBRFClass as $prop) {
            if (!property_exists($this->MBRFClass, $prop)) {
                throw new PropertyNotFoundException($prop, get_class($this->MBRFClass));
            }
            $getter = 'get' . ucfirst($prop);
            if (!method_exists($this->MBRFClass, $getter)) {
                throw new MethodNotFoundException($getter, get_class($this->MBRFClass));
            }
        }

        foreach ($ymlMBRFProp as $prop) {
            if (!property_exists($this->MBRFProp, $prop)) {
                throw new PropertyNotFoundException($prop, get_class($this->MBRFProp));
            }
            $getter = 'get' . ucfirst(strtolower($prop));
            if (!method_exists($this->MBRFProp, $getter)) {
                throw new MethodNotFoundException($getter, get_class($this->MBRFProp));
            }
        }
    }

    function getMandatoryProperties(string $class) : array
    {
        return array_keys(array_filter($this->ymlConfig[$class], function ($prop) {
            return (isset($prop['mandatory']) && $prop['mandatory'] === true)? true : false;
        }));
    }
}