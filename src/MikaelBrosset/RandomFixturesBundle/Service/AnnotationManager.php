<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use Doctrine\Common\Annotations\Reader;
use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFClass;
use MikaelBrosset\RandomFixturesBundle\Exception\MethodNotFoundException;
use MikaelBrosset\RandomFixturesBundle\Exception\PropertyNotFoundException;
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
        $entityObj = $this->loadClassFromNamespace($file);
        $r = new \ReflectionClass(new $entityObj());

        //Number of times a class will be copied in db
        $times = $this->getClassAnnotations($r); // TODO here

        //The annotations coming from entity properties
        $propAnot = $this->getPropertiesAnnotations($r);

        for ($i = 1; $i<= $times; $i++) {
            $entity = new $entityObj();
            foreach ($propAnot as $name => $values) {
                $method = 'set'. ucfirst($name);
                $entity->$method($this->mapping[$values['type']]->getValue($values['type'], $values['option']));
            }

            $this->persist($entity);
        }
        $this->flush();
    }

    private function getClassAnnotations(\ReflectionClass $entityR): array
    {
        $MBRFClass  = new MBRFClass();
        $MBRFClassR = new \ReflectionClass($MBRFClass);

        // Fills $MBRFClass properties with data from annotations
        $MBRFClassFilled = $this->reader->getClassAnnotation($entityR, $MBRFClassR->getName());

        //die(var_dump(in_array('times', $MBRFClass->getMandatoryProperties())));
        /** verif à faire dans l'entité (ex pour FR, EN, etc) + correctif $data*/

        // Gets all properties from MBRFClass model...
        $MBRFClassReflectProp = $MBRFClassR->getProperties();

        // ... and matches them with the entity ones
        $data = [];
        for ($i = 0; $i<count($MBRFClassReflectProp); $i++) {
            $MBRFClassProp[$i]     = $MBRFClassReflectProp[$i]->getName();
            $MBRFClassMethodToCall = 'get' . ucfirst($MBRFClassProp[$i]);

            // Validates the MBRFClass mandatory properties are set
            if (in_array($MBRFClassProp[$i], $MBRFClass->getMandatoryProperties()) && is_null($MBRFClassFilled->$MBRFClassMethodToCall())) {
                throw new PropertyNotFoundException($MBRFClassProp[$i], $entityR->getName());
            }

            //Validates the MBRFClass mandatory method getter for the forenamed property is set
            if (!method_exists($MBRFClassFilled, $MBRFClassMethodToCall)) {
                throw new MethodNotFoundException($MBRFClassMethodToCall, get_class($MBRFClassFilled));
            }

            $data[$MBRFClassProp[$i]] = $MBRFClass->$MBRFClassMethodToCall();
            var_dump($MBRFClass->$MBRFClassMethodToCall());
        }
        return $data;
    }

    private function getPropertiesAnnotations(\ReflectionClass $reflectClass): array
    {
        $annot = [];
        foreach ($reflectClass->getProperties() as $prop) {

            if (!is_null($reflProp = $this->reader->getPropertyAnnotation($prop, 'MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp'))) {
                $annot[$prop->name] = [
                    'type'   => $reflProp->type,
                    'null'   => $reflProp->null,
                    'option' => $reflProp->option
                ];
            }
        }
        return $annot;
    }
}