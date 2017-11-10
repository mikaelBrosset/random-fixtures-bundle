<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 08/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use MikaelBrosset\RandomFixturesBundle\Exception\NamespaceNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class EntityFinder extends Manager
{
    /**
     * Find php Files in Entity folder and subfolders
     */
    protected function findFiles()
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
}