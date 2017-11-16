<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 08/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use MikaelBrosset\RandomFixturesBundle\Exception\MethodNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\MissingMandatoryPropertyParameterException;
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
    protected function findEntities() : \RegexIterator
    {
        $f = new Finder();
        $f->files()
            ->in($this->absDir);

        $it = $f->files()->getIterator();
        $entIt = new \RegexIterator($it, "#Entity\/\w+\/*\.php$#");

        if (is_null(!$entIt->valid())) {
            $this->output->writeln(sprintf("<error>No php Entity found in %s<error>", $this->absDir));
            exit();
        }
        return $entIt;
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

    /////////////////////YML CONFIG ////////////////////////

    /**
     * Maps the annotation name with their Generator
     */
    function parseYmlConfig()
    {
        $this->ymlConfig = Yaml::parse(file_get_contents($this->absDir . 'MikaelBrosset/RandomFixturesBundle/Config/config.yml'));
    }

    function getMandatoryProperties(array $classes) : array
    {
        $mandatProps = [];
        foreach ($classes as $key => $item) {
            $mandatProps[$key] = array_keys(array_filter($this->ymlConfig[$key], function ($prop) {
                return (isset($prop['mandatory']) && $prop['mandatory'] === true)? true : false;
            }));
        }
        return $mandatProps;
    }

    function validateMandatoryProperties($mandatoryProps, $annotProps)
    {
        foreach ($mandatoryProps as $mProp) {
            if (!in_array($mProp, $annotProps)) {
                throw new MissingMandatoryPropertyParameterException();
            }
        }
    }

    ///TODO retourner uniquement les resources dont on a besoin
    function getNeededResourcesFromConfig(array $needed) : array
    {
        //for test only
        $needed = ['femaleFirstname', 'maleFirstname', 'lastname'];

        $config = $this->ymlConfig['MBRFProp']['type']['generators'];
        $resourcesKeys = array_filter($config, function ($data) {
            return (array_key_exists('resource', $data)) ? true : false;
        });
        $finalresourcesKeys = array_filter($resourcesKeys, function ($data) use ($needed) {
            return in_array($data, $needed) ? true : false;
        });

        die(var_dump($finalresourcesKeys));
    }

    function loadResources($resourcesToLoad)
    {

    }
}