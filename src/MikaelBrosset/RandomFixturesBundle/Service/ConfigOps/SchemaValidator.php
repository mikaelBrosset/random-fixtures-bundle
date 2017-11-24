<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 17/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service\ConfigOps;

use MikaelBrosset\RandomFixturesBundle\Exception\MappingNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\MethodNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\PropertyNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\ResourceNotFoundException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Debug\Exception\ClassNotFoundException;

class SchemaValidator
{
    private $ymlConfig;
    private $MBRFClasses;
    private $resourcesDir;

    public function __construct($ymlConfig, $MBRFClasses, $absDir)
    {
        $this->ymlConfig    = $ymlConfig;
        $this->MBRFClasses  = $MBRFClasses;
        $this->absDir       = $absDir;
        $this->resourcesDir = $absDir . 'MikaelBrosset/RandomFixturesBundle/Generators/Resources/';
    }

    function validateMBRFPropertiesAndSetters() : SchemaValidator
    {
        $ymlMBRFClass = array_keys($this->ymlConfig['MBRF']['MBRFClass']);
        $ymlMBRFProp  = array_keys($this->ymlConfig['MBRF']['MBRFProp']);

        foreach ($ymlMBRFClass as $prop) {
            if (!property_exists($this->MBRFClasses['MBRFClass'], $prop)) {
                throw new PropertyNotFoundException($prop, get_class($this->MBRFClasses['MBRFClass']));
            }
            $getter = 'get' . ucfirst($prop);
            if (!method_exists($this->MBRFClasses['MBRFClass'], $getter)) {
                throw new MethodNotFoundException($getter, get_class($this->MBRFClasses['MBRFClass']));
            }
        }

        foreach ($ymlMBRFProp as $prop) {
            if (!property_exists($this->MBRFClasses['MBRFProp'], $prop)) {
                throw new PropertyNotFoundException($prop, get_class($this->MBRFClasses['MBRFProp']));
            }
            $getter = 'get' . ucfirst(strtolower($prop));
            if (!method_exists($this->MBRFClasses['MBRFProp'], $getter)) {
                throw new MethodNotFoundException($getter, get_class($this->MBRFClasses['MBRFProp']));
            }
        }
        return $this;
    }
    public function getMandatoryProperties() : array
    {
        $mandatProps = [];
        foreach ($this->MBRFClasses as $key => $item) {
            $mandatProps[$key] = array_keys(array_filter($this->ymlConfig['MBRF'][$key], function ($prop) {
                return (isset($prop['mandatory']) && $prop['mandatory'] === true)? true : false;
            }));
        }
        return $mandatProps;
    }

    public function validatesGeneratorAndResources() : SchemaValidator
    {
        foreach ($this->ymlConfig['MBRF']['MBRFProp']['type']['generators'] as $name => $g) {

            if (isset($g['resource'])) {
                $resourceFile = $this->resourcesDir . $g['resource'];

                if (!file_exists($resourceFile) && !is_readable($resourceFile)){
                    throw new ResourceNotFoundException($g['resource'], $this->resourcesDir);
                }
            }
            if (!isset($g['mapping'])) {
                throw new MappingNotFoundException($name);
            }
            // checks if mapping class is instanciable, else throw Symfony ClassNotFoundException
            //new $g['mapping'];
        }

        return $this;
    }
}