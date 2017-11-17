<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 17/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service\EntityAnnotationProcessor;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFClass;
use MikaelBrosset\RandomFixturesBundle\Exception\MissingMandatoryParameterException;

class ClassAnnotationProcessor extends PropertiesAnnotationProcessor
{
    /**
     * Gets annotation values from an entity and checks for mandatory properties (ex: times)
     */
    public function getEntityClassAnnotations() : array
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

            $this->checksMandatoryAnnotationNotNull($MBRFClassFilled, $MBRFClassMethodToCall, $MBRFClassProp[$i]);
            $data[$MBRFClassProp[$i]] = $MBRFClassFilled->$MBRFClassMethodToCall();
        }
        return $data;
    }

    function checksMandatoryAnnotationNotNull(MBRFClass $MBRFFilled, string $MBRFClassMethodToCall, string $classProperty): void
    {
        $mp = $this->mandatoryProps['MBRFClass'];

        if (is_null($MBRFFilled->$MBRFClassMethodToCall()) && in_array($classProperty, $mp)) {
            throw new MissingMandatoryParameterException($classProperty, $this->entityR->getName());
        }
    }

}