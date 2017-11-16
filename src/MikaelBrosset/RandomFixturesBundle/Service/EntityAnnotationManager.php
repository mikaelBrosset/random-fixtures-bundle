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
use MikaelBrosset\RandomFixturesBundle\Exception\ListNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\MissingMandatoryParameterException;
use MikaelBrosset\RandomFixturesBundle\Exception\MissingMandatoryPropertyParameterException;
use Symfony\Component\Finder\SplFileInfo;

class EntityAnnotationManager extends FileManager
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

        /** verif à faire dans l'entité (ex pour FR, EN, etc) //// et matcher le yml avec l'entité */
        //The annotations coming from class properties (ex: Number of times a class will be copied in db)
        $classAnnot = $this->getEntityClassAnnotations();

        //The annotations coming from entity properties
        $propAnnot  = $this->getEntityPropertiesAnnotations();

        //$resources = []; TODO

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
    private function getEntityClassAnnotations(): array
    {
        $data = [];

        // Fills $MBRFClass properties with data from annotations
        $MBRFClassFilled = $this->reader->getClassAnnotation(new \ReflectionClass($this->entity), $this->MBRFClassesReflect['MBRFClassR']->getName());

        // Gets all properties from MBRFClass model...
        $MBRFClassRProp = $this->MBRFClassesReflect['MBRFClassR']->getProperties();

        // ... and matches them with the entity ones
        for ($i = 0; $i<count($MBRFClassRProp); $i++) {
            $MBRFClassProp[$i]     = $MBRFClassRProp[$i]->getName();
            $MBRFClassMethodToCall = 'get' . ucfirst($MBRFClassProp[$i]);

            $this->checksMandatoryClassAnnotationNotNull($MBRFClassFilled, $MBRFClassMethodToCall, $MBRFClassProp[$i]);
            $data[$MBRFClassProp[$i]] = $MBRFClassFilled->$MBRFClassMethodToCall();
        }
        return $data;
    }

    function checksMandatoryClassAnnotationNotNull(MBRFClass $MBRFFilled, string $MBRFClassMethodToCall, string $classProperty): void
    {
        $mp = $this->mandatoryProps['MBRFClass'];

        if (is_null($MBRFFilled->$MBRFClassMethodToCall()) && in_array($classProperty, $mp)) {
            throw new MissingMandatoryParameterException($classProperty, $this->entityR->getName());
        }
    }

    private function getEntityPropertiesAnnotations()
    {
        $data = [];
        $entityRProps   = (new \ReflectionClass($this->entity))->getProperties();
        for ($i=0; $i<count($entityRProps); $i++) {

            // If @MBRF annotation is mission on property, loop continues without it
            if (!$MBRFPropFilled = $this->getEntityPropertyAnnotations($entityRProps[$i])) { continue; };

            $this->checksMandatoryAnnotationPropertiesNotNull($MBRFPropFilled, $entityRProps[$i]->getName());
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



    function checksMandatoryAnnotationPropertiesNotNull(MBRFProp $MBRFFilled, string $propertyName): void
    {
        $mp = $this->mandatoryProps['MBRFProp'];

        for($i=0; $i<count($MBRFprops = $this->MBRFClassesReflect['MBRFPropR']->getProperties()); $i++) {
            $getter = 'get' . ucfirst($MBRFprops[$i]->getName());

            if (is_null($MBRFFilled->$getter()) && in_array($MBRFprops[$i]->getName(), $mp)) {
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