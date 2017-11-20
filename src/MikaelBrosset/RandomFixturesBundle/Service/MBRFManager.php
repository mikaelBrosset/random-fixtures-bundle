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
use MikaelBrosset\RandomFixturesBundle\Service\ConfigOps\SchemaValidator;
use MikaelBrosset\RandomFixturesBundle\Service\ConfigOps\YamlGetter;
use MikaelBrosset\RandomFixturesBundle\Service\EntityAnnotationProcessor\EntityAnnotationManager;
use Symfony\Component\Console\Output\OutputInterface;

class MBRFManager
{
    protected $output;
    protected $ema;
    protected $MBRFClasses;
    protected $MBRFClassesReflect;

    public function __construct(OutputInterface $output, $projectDir, EntityManager $em, $dir = '/src/')
    {
        $this->output = $output;
        $ema = new EntityManagerAdapter($em);
        $this->absDir = $projectDir . $dir;
        $this->reader = new AnnotationReader();
    }

    public function manage()
    {
        $ymlConfig = (new YamlGetter($this->absDir))->parseYmlConfig();

        $this->instanceMBRFClasses();

        $SchemaValidator = new SchemaValidator($ymlConfig, $this->MBRFClasses, $this->absDir);
        $mandatoryProps = $SchemaValidator
            ->validateMBRFPropertiesAndSetters()
            //->validatesGeneratorFiles()
            ->getMandatoryProperties();

        // Cycle through every entity file
        $entitiesIterator = (FileManager::findEntities($this->absDir, $this->output));

        foreach ($entitiesIterator as $e) {
            (new EntityAnnotationManager($e, $ymlConfig, $this->ema, $mandatoryProps, $this->MBRFClasses, $this->MBRFClassesReflect))->manage();
        }
    }

    protected function instanceMBRFClasses() : void
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
    }
}