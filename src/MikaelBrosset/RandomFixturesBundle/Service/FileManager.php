<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 08/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use MikaelBrosset\RandomFixturesBundle\Exception\NamespaceNotFoundException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileManager
{
    /**
     * Find php Files in Entity folder and subfolders
     */
    public static function findEntities($absDir, OutputInterface $output) : \RegexIterator
    {
        $f = new Finder();
        $f->files()
            ->in($absDir);

        $it = $f->files()->getIterator();
        $entIt = new \RegexIterator($it, "#Entity\/\w+\/*\.php$#");

        if (is_null(!$entIt->valid())) {
            $output->writeln(sprintf("<error>No php Entity found in %s<error>", $absDir));
            exit();
        }
        return $entIt;
    }

    /**
     * Turns a pathname into a namespace according to PSR-4
     */
    public static function loadClassFromNamespace(SplFileInfo $file)
    {
        $namespace = str_replace('/', '\\', substr($file->getRelativePathname(), 0, strpos($file->getRelativePathname(), '.php')));
        if (!is_object($c = new $namespace)) {
            throw new NamespaceNotFoundException($c);
        }
        return $c;
    }

    /////////////////////YML CONFIG ////////////////////////


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
}