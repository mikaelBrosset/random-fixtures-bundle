<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service\EntityAnnotationProcessor;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use MikaelBrosset\RandomFixturesBundle\Generators\Generator;
use MikaelBrosset\RandomFixturesBundle\Generators\Group;
use MikaelBrosset\RandomFixturesBundle\Service\EntityManagerAdapter;
use MikaelBrosset\RandomFixturesBundle\Service\FileManager;
use Symfony\Component\Finder\SplFileInfo;

class EntityAnnotationManager extends ClassAnnotationProcessor
{
    /**
     * @var Reader
     */
    protected $entityClassName;
    protected $entity;
    protected $mandatoryProps;
    protected $reader;
    protected $ymlConfig;
    protected $ema;
    protected $MBRFClasses;
    protected $MBRFClassesReflect;
    private   $loadedGenerators = [];
    private   $loadedResources = [];

    public function __construct(SplFileInfo $file, array $ymlConfig, $ema, $mandatoryProps, $MBRFClasses, $MBRFClassesReflect)
    {
        //Loads an entity from filename (ex: AppBundle\User)
        $this->entityClassName = FileManager::loadClassFromNamespace($file);
        $this->entity = new $this->entityClassName();
        $this->reader = new AnnotationReader();
        $this->mandatoryProps = $mandatoryProps;
        $this->ymlConfig = $ymlConfig;
        $this->ema = $ema;

        $this->MBRFClasses  = $MBRFClasses;
        $this->MBRFClassesReflect = $MBRFClassesReflect;
    }

    /**
     * Manage Annotation for One Entity
     */
    public function manage()
    {
        //The annotations coming from class properties (ex: Number of times a class will be copied in db)
        $classAnnot = $this->getEntityClassAnnotations();

        //The annotations coming from entity properties
        $propAnnot  = $this->getEntityPropertiesAnnotations();

        $this->callGeneratorAndSave($classAnnot, $propAnnot);
    }

    public function callGeneratorAndSave($classAnnot, $propAnnot)
    {
        $ymlGenerators = $this->ymlConfig['MBRF']['MBRFProp']['type']['generators'];

        for ($i = 1; $i<= $classAnnot['times']; $i++) {
            $entity = new $this->entityClassName();
            foreach ($propAnnot as $name => $MBRFProp) {

                // Generator is called according to the config mapping and stored for further use by the entity
                $generatorName  = $MBRFProp->getType();
                $generator = $this->loadResourceAndGenerator($ymlGenerators, $generatorName);
                $entityMethod = 'set'. ucfirst($name);

                $value = $generator
                    ->calculateValue($MBRFProp)
                    ->getValue();

                $entity->$entityMethod($value);
                //die(var_dump($value));
                //die();
            }
            $this->ema->persist($entity);
        }
        $this->ema->flush();
    }

    private function loadResourceAndGenerator(array $ymlGenerators, string $generatorName) :  Generator
    {
        $ymlGenerator   = $ymlGenerators[$generatorName];
        $generatorClass = $ymlGenerator['mapping'];
        $resourceName   = isset($ymlGenerators[$generatorName]['resource']) ? $ymlGenerators[$generatorName]['resource'] : [];

        $generator = $this->eagerLoadGenerator($generatorName, $generatorClass, $ymlGenerator);
        if (isset($ymlGenerator['resource'])) {
            $this->eagerLoadResource($generatorName, $generator, $resourceName);
        }
        return $generator;
    }

    private function eagerLoadGenerator($generatorName, $generatorClass, $ymlGenerator)
    {
        if (!isset($this->loadedGenerators[$generatorName])) {
            $this->loadedGenerators[$generatorName] =
                $this->loadedGenerators[$generatorName] ??
                $generator = (new $generatorClass())
                    ->setName($generatorName)
                    ->setResourcePath($ymlGenerator['mapping']);
            if (isset($ymlGenerator['resource'])) {
                $generator->setResourceName($ymlGenerator['resource']);
            }
        } else {
            $generator = $this->loadedGenerators[$generatorName];
        }
        return $generator;
    }

    private function eagerLoadResource($generatorName, $generator, $resourceName)
    {
        if (!isset($this->loadedResources[$generatorName])) {
            $this->loadedResources[$generatorName] = $generator->openAndSetResourceList($resourceName);
        } else {
            $generator->setResourceList($this->loadedResources[$generatorName]);
        }
    }
}