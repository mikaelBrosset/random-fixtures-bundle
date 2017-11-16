<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use Doctrine\Common\Annotations\Reader;
use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;
use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\MissingMandatoryParameterException;
use MikaelBrosset\RandomFixturesBundle\Exception\MissingMandatoryPropertyParameterException;
use Symfony\Component\Finder\SplFileInfo;

class AnnotationManager extends FileManager
{
    /**
     * @var Reader
     */
    protected $reader;
    protected $entity;
    protected $entityR;

    /**
     * Manage Annotation for One Entity
     */
    protected function manageAnnotation(SplFileInfo $file)
    {
        //Loads an entity from filename (ex: AppBundle\User)
        $entityClassName = $this->loadClassFromNamespace($file);
        $this->entity  = new $entityClassName();
        //$entityR = new \ReflectionClass($entity());

        /** verif à faire dans l'entité (ex pour FR, EN, etc) //// et matcher le yml avec l'entité  Checks ths properties have an actual getter */
        //The annotations coming from class properties (ex: Number of times a class will be copied in db)
        //$classAnnot = $this->getEntityClassAnnotations($entity);

        //The annotations coming from entity properties
        $propAnnot  = $this->getEntityPropertiesAnnotations();
die(var_dump($propAnnot));
die();






        for ($i = 1; $i<= $classAnnot['times']; $i++) {
            $entity = new $entityClassName();
            foreach ($propAnnot as $name => $MBRFProp) {
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
    private function getClassAnnotations(): array
    {
        // Fills $MBRFClass properties with data from annotations
        $MBRFClassFilled = $this->reader->getClassAnnotation($this->entityR, $this->MBRFClassR->getName());

        // Gets all properties from MBRFClass model...
        $MBRFClassRProp = $this->MBRFClassR->getProperties();

        $data = [];
        $resources = [];

        // ... and matches them with the entity ones
        for ($i = 0; $i<count($MBRFClassRProp); $i++) {
            $MBRFClassProp[$i]     = $MBRFClassRProp[$i]->getName();
            $MBRFClassMethodToCall = 'get' . ucfirst($MBRFClassProp[$i]);

            $mp = $this->getMandatoryProperties('MBRFClass');

            if (in_array($MBRFClassProp[$i], $mp) && is_null($MBRFClassFilled->$MBRFClassMethodToCall())) {
                throw new MissingMandatoryParameterException($MBRFClassProp[$i], $this->entityR->getName());
            }
            $data[$MBRFClassProp[$i]] = $MBRFClassFilled->$MBRFClassMethodToCall();
        }
        return $data;
    }

    private function getEntityPropertiesAnnotations()
    {
        $data = [];
        $entityRProps   = (new \ReflectionClass($this->entity))->getProperties();
        for ($i=0; $i<count($entityRProps); $i++) {

            // If @MBRF annotation is mission on property, loop continues without it
            if (!$MBRFPropFilled = $this->getEntityPropertyAnnotations($entityRProps[$i])) { continue; };

            $this->checksMandatoryPropertiesNotNull($MBRFPropFilled, $entityRProps[$i]->getName());
            $data[$entityRProps[$i]->getName()] = $MBRFPropFilled;
        }
        return $data;
    }

    /**
     * Gets annotation values from an entity properties and checks for mandatory properties (ex: type)
     */
    private function getEntityPropertyAnnotations($entityProperty)
    {
        // Fills MBRFProp with its values coming from annotation
        $MBRFPropFilled = $this->reader->getPropertyAnnotation($entityProperty, $this->MBRFClasses['MBRFProp']);

        // Checks the entity property has a @MBRF annotation
        if (is_null($MBRFPropFilled)) { return false; }

        return $MBRFPropFilled;
    }

    function checksMandatoryPropertiesNotNull(MBRFProp $MBRFPropFilled, string $propertyName): void
    {
        $mp = $this->mandatoryProps['MBRFProp'];

        for($i=0; $i<count($MBRFprops = $this->MBRFPropR->getProperties()); $i++) {
            $getter = 'get' . ucfirst($MBRFprops[$i]->getName());

            if (is_null($MBRFPropFilled->$getter()) && in_array($MBRFprops[$i]->getName(), $mp)) {
                throw new MissingMandatoryPropertyParameterException($MBRFprops[$i]->getName(), get_class($this->entity), $propertyName);
            }
        }
    }

    public function eagerLoadResources($file)
    {
        $resources = [];
        return $resources;
    }

    public function eagerLoadGenerators($namespace)
    {
        $generators = [];
        return $generators;
    }
}