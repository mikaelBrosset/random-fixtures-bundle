<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 17/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service\EntityAnnotationProcessor;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;
use MikaelBrosset\RandomFixturesBundle\Exception\MissingMandatoryPropertyParameterException;

class PropertiesAnnotationProcessor
{
    /**
     * Gets annotation values from an entity properties and checks for mandatory properties (ex: type)
     */
    public function getEntityPropertiesAnnotations() : array
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
}