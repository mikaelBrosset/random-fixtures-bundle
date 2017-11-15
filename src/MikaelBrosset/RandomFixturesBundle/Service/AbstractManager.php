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
    protected $MBRFClass;
    protected $MBRFProp;

    public function __construct(OutputInterface $output, $projectDir, EntityManager $em, $dir = '/src/')
    {
        $this->output = $output;
        $this->em = $em;
        $this->absDir = $projectDir . $dir;
        $this->reader = new AnnotationReader();
    }

    public function manage()
    {
        $rit = $this->findFiles($this->absDir);
        $this->parseYmlConfig();

        $this->instanceMBRFClasses();
        $this->validatePropertiesAndSetters();
        // Cyclee through every entity file
        foreach ($rit as $r) {
            $this->manageAnnotation($r);
        }
    }

    protected function instanceMBRFClasses() : void
    {
        $this->MBRFClass = new MBRFClass();
        $this->MBRFProp  = new MBRFProp();

        $this->MBRFClassR = new \ReflectionClass($this->MBRFClass);
        $this->MBRFPropR  = new \ReflectionClass($this->MBRFProp);
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