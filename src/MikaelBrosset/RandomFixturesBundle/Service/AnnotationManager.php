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
use Symfony\Component\Console\Output\OutputInterface;
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
            die(var_dump($times));
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
        $MBRFClass = new MBRFClass();
        $MBRFClassReflect = new \ReflectionClass($MBRFClass);

        // Fills $MBRFClass properties with data from annotations
        $MBRFClass = $this->reader->getClassAnnotation($entityR, $MBRFClassReflect->getName());


        //die(var_dump(in_array('times', $MBRFClass->getMandatoryProperties())));
        /** TODO VALIDATION QU' il y ait bien name + verif à faire dans l'entité (ex pour FR, EN, etc)*/

        // Gets all properties from MBRFClass model...
        $MBRFClassReflectProp = $MBRFClassReflect->getProperties();

        // ... and matches them with the entity ones
        $data = [];
        for ($i = 0; $i<count($MBRFClassReflectProp); $i++) {
            $MBRFClassProp[$i] = $MBRFClassReflectProp[$i]->getName();
            $MBRFClassMethodToCall = 'get' . ucfirst($MBRFClassProp[$i]);

            if (!method_exists($MBRFClass, $MBRFClassMethodToCall)) {
                throw new MethodNotFoundException($MBRFClassMethodToCall, get_class($MBRFClass));
            }

            $data[$MBRFClassProp[$i]] = $MBRFClass->$MBRFClassMethodToCall();
        }
        //die(var_dump($data));
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