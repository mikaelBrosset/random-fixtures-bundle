<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use Doctrine\Common\Annotations\Reader;
use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFClass;
use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;
use MikaelBrosset\RandomFixturesBundle\Exception\{
    MethodNotFoundException, MissingMandatoryParameterException, MissingMandatoryPropertyParameterException, PropertyNotFoundException
};
use MikaelBrosset\RandomFixturesBundle\Generators;

use Symfony\Component\Finder\SplFileInfo;

class AnnotationManager extends EntityFinder
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Manage Annotation for One Entity
     */
    protected function manageAnnotation(SplFileInfo $file)
    {
        //Loads an entity from filename (ex: AppBundle\User)
        $entityClassName = $this->loadClassFromNamespace($file);
        $entity = new $entityClassName();

        /** verif à faire dans l'entité (ex pour FR, EN, etc) //// et matcher le yml avec l'entité  Checks ths properties have an actual getter */
        //Number of times a class will be copied in db
        $classAnnot = $this->getClassAnnotations($entity, new MBRFClass());

        //The annotations coming from entity properties
        $propAnot = $this->getPropertyAnnotations($entity, new MBRFProp());

        for ($i = 1; $i<= $classAnnot['times']; $i++) {
            $entity = new $entityClassName();
            foreach ($propAnot as $name => $MBRFProp) {
                $method = 'set'. ucfirst($name);

                // Generator is called according to the config mapping
                $generatorToCall = 'MikaelBrosset\RandomFixturesBundle\\' .  $this->ymlConfig['MBRFProp']['type']['generators'][$MBRFProp->getType()]['mapping'];
                $entity->$method((new $generatorToCall())->getValue($MBRFProp));
            }
            $this->persist($entity);
        }
        $this->flush();
    }

    /**
     * Gets annotation values from an entity and checks for mandatory properties (ex: times)
     */
    private function getClassAnnotations($entity, MBRFClass $MBRFClass): array
    {
        $entityR    = new \ReflectionClass(new $entity());
        $MBRFClassR = new \ReflectionClass($MBRFClass);

        // Fills $MBRFClass properties with data from annotations
        $MBRFClassFilled = $this->reader->getClassAnnotation($entityR, $MBRFClassR->getName());

        // Gets all properties from MBRFClass model...
        $MBRFClassReflectProp = $MBRFClassR->getProperties();

        // ... and matches them with the entity ones
        $data = [];
        for ($i = 0; $i<count($MBRFClassReflectProp); $i++) {
            $MBRFClassProp[$i]     = $MBRFClassReflectProp[$i]->getName();
            $MBRFClassMethodToCall = 'get' . ucfirst($MBRFClassProp[$i]);

            $mp = $this->getMandatoryProperties('MBRFClass');

            if (in_array($MBRFClassProp[$i], $mp) && is_null($MBRFClassFilled->$MBRFClassMethodToCall())) {
                throw new MissingMandatoryParameterException($MBRFClassProp[$i], $entityR->getName());
            }
            $data[$MBRFClassProp[$i]] = $MBRFClassFilled->$MBRFClassMethodToCall();
        }
        return $data;
    }

    /**
     * Gets annotation values from an entity properties and checks for mandatory properties (ex: type)
     */
    private function getPropertyAnnotations($entity, MBRFProp $MBRFProp): array
    {
        $data = [];
        $entityRProps   = (new \ReflectionClass($entity))->getProperties();
        $MBRFPropRProps = (new \ReflectionClass($MBRFProp))->getProperties();

        for ($i=0; $i<count($entityRProps); $i++) {

            // Fills MBRFProp with its values coming from annotation
            $MBRFPropFilled = $this->reader->getPropertyAnnotation($entityRProps[$i], $MBRFProp);

            // Checks the entity property has a @MBRF annotation
            if (is_null($MBRFPropFilled)) { continue; }

            // Checks mandatory properties are not null
            $mp = $this->getMandatoryProperties('MBRFProp');
            for($m=0; $m<count($mp); $m++) {
                $getter = 'get' . ucfirst($mp[$m]);

                if (is_null($MBRFPropFilled->$getter())) {
                    throw new MissingMandatoryPropertyParameterException($mp[$m], get_class($entity), $entityRProps[$i]->getName());
                }
            }
            $data[$entityRProps[$i]->getName()] = $MBRFPropFilled;
        }
        return $data;
    }

    function getMandatoryProperties(string $class) : array
    {
        return array_keys(array_filter($this->ymlConfig[$class], function ($prop) {
            return (isset($prop['mandatory']) && $prop['mandatory'] === true)? true : false;
        }));
    }

    function validatePropertiesAndSetters() : void
    {
        $MBRFClass = new MBRFClass();
        $MBRFProp  = new MBRFProp();

        $ymlMBRFClass = array_keys($this->ymlConfig['MBRFClass']);
        $ymlMBRFProp  = array_keys($this->ymlConfig['MBRFProp']);

        foreach ($ymlMBRFClass as $prop) {
            if (!property_exists($MBRFClass, $prop)) {
                throw new PropertyNotFoundException($prop, get_class($MBRFClass));
            }
            $getter = 'get' . ucfirst($prop);
            if (!method_exists($MBRFClass, $getter)) {
                throw new MethodNotFoundException($getter, get_class($MBRFClass));
            }
        }

        foreach ($ymlMBRFProp as $prop) {
            if (!property_exists($MBRFProp, $prop)) {
                throw new PropertyNotFoundException($prop, get_class($MBRFProp));
            }
            $getter = 'get' . ucfirst(strtolower($prop));
            if (!method_exists($MBRFProp, $getter)) {
                throw new MethodNotFoundException($getter, get_class($MBRFProp));
            }
        }
    }
}