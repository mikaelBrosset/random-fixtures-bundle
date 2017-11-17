<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 17/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service\ConfigOps;

use Symfony\Component\Yaml\Yaml;

class YamlGetter
{
    private $absDir;

    public function __construct($absDir)
    {
        $this->absDir = $absDir;
    }

    /**
     * Maps the annotation name with their Generator
     */
    public function parseYmlConfig()
    {
        return Yaml::parse(file_get_contents($this->absDir . 'MikaelBrosset/RandomFixturesBundle/Config/config.yml'));
    }
}