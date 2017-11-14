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
    MethodNotFoundException,
    MissingMandatoryParameterException,
    MissingMandatoryPropertyParameterException,
    PropertyNotFoundException
};

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

        /** verif à faire dans l'entité (ex pour FR, EN, etc)*/
        //Number of times a class will be copied in db
        $classAnnot = $this->getClassAnnotations($entity, new MBRFClass());

        //The annotations coming from entity properties
        $propAnot = $this->getPropertyAnnotations($entity, new MBRFProp());

        /**
         * TODO MAPPING YML
         */

        for ($i = 1; $i<= $classAnnot['times']; $i++) {
            $entity = new $entityClassName();
            foreach ($propAnot as $name => $MBRFProp) {
                $method = 'set'. ucfirst($name);
                $entity->$method($this->mapping[$MBRFProp->getType()]->getValue($MBRFProp));
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

            // Validates the setting of MBRFClass mandatory properties
            if (in_array($MBRFClassProp[$i], $MBRFClass->getMandatoryProperties()) && is_null($MBRFClassFilled->$MBRFClassMethodToCall())) {
                throw new MissingMandatoryParameterException($MBRFClassProp[$i], $entityR->getName());
            }

            // Validates the setting for the MBRFClass mandatory getter method matched by the forenamed property
            if (!method_exists($MBRFClassFilled, $MBRFClassMethodToCall)) {
                throw new MethodNotFoundException($MBRFClassMethodToCall, get_class($MBRFClassFilled));
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

            // Checks ths properties have an actual getter
            for ($j=0; $j<count($MBRFPropRProps); $j++) {

                $getter = 'get' . ucfirst($MBRFPropRProps[$j]->getName());
                if (!method_exists($MBRFPropFilled, $getter)) {
                    throw new MethodNotFoundException($getter, get_class($MBRFPropFilled));
                }
            }

            // Checks mandatory properties are set
            $mandatoryProps = $MBRFProp->getMandatoryProperties();
            for($m=0; $m<count($mandatoryProps); $m++) {
                $getter = 'get' . ucfirst($mandatoryProps[$m]);

                if (is_null($MBRFPropFilled->$getter())) {
                    throw new MissingMandatoryPropertyParameterException($mandatoryProps[$m], get_class($entity), $entityRProps[$i]->getName());
                }
            }

            $data[$entityRProps[$i]->getName()] = $MBRFPropFilled;
        }
        return $data;
    }
}