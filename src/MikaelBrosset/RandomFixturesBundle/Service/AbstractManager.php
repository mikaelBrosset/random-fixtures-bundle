<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFClass;
use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractManager
{
    protected $output;
    protected $em;
    protected $MBRFClasses;
    protected $MBRFClassesReflect;
    protected $mandatoryProps;

    public function __construct(OutputInterface $output, $projectDir, EntityManager $em, $dir = '/src/')
    {
        $this->output = $output;
        $this->em = $em;
        $this->absDir = $projectDir . $dir;
        $this->reader = new AnnotationReader();
    }

    public function manage()
    {
        $entitiesIterator = $this->findEntities($this->absDir);
        $this->parseYmlConfig();
        $this->instanceAndValidateMBRFClasses();

        $this->mandatoryProps = $this->getMandatoryProperties($this->MBRFClasses);

        // Cycle through every entity file
        foreach ($entitiesIterator as $e) {
            $this->manageAnnotation($e);
        }
    }

    protected function instanceAndValidateMBRFClasses() : void
    {
        $MBRFClass = new MBRFClass();
        $MBRFProp  = new MBRFProp();
        $this->MBRFClasses = [
            'MBRFClass' => $MBRFClass,
            'MBRFProp'  => $MBRFProp
        ];

        $this->MBRFClassesReflect = [
            'MBRFClassR' => new \ReflectionClass($MBRFClass),
            'MBRFPropR'  => new \ReflectionClass($MBRFProp)
        ];

        $this->validateMBRFPropertiesAndSetters();
    }

    function validateMBRFPropertiesAndSetters() : void
    {
        $ymlMBRFClass = array_keys($this->ymlConfig['MBRFClass']);
        $ymlMBRFProp  = array_keys($this->ymlConfig['MBRFProp']);

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
    }

    protected function persist($data)
    {
        $this->em->persist($data);
    }

    protected function flush()
    {
        $this->em->flush();
        $this->em->clear();
    }
}