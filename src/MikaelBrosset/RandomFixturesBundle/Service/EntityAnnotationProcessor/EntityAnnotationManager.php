<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service\EntityAnnotationProcessor;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
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
    protected $em;
    protected $MBRFClasses;
    protected $MBRFClassesReflect;

    public function __construct(SplFileInfo $file, array $ymlConfig, $em, $mandatoryProps, $MBRFClasses, $MBRFClassesReflect)
    {
        //Loads an entity from filename (ex: AppBundle\User)
        $this->entityClassName = FileManager::loadClassFromNamespace($file);
        $this->entity = new $this->entityClassName();
        $this->reader = new AnnotationReader();
        $this->mandatoryProps = $mandatoryProps;
        $this->ymlConfig = $ymlConfig;
        $this->em = $em;

        $this->MBRFClasses  = $MBRFClasses;
        $this->MBRFClassesReflect = $MBRFClassesReflect;
    }

    /**
     * Manage Annotation for One Entity
     */
    public function manage()
    {
        /** TODO verif à faire dans l'entité (ex pour FR, EN, etc)*/
        //The annotations coming from class properties (ex: Number of times a class will be copied in db)
        $classAnnot = $this->getEntityClassAnnotations();

        //The annotations coming from entity properties
        $propAnnot  = $this->getEntityPropertiesAnnotations();

        //$resources = []; TODO
        $this->callGeneratorAndSave($classAnnot, $propAnnot);
    }

    public function callGeneratorAndSave($classAnnot, $propAnnot)
    {
        $ema = new EntityManagerAdapter($this->em);

        for ($i = 1; $i<= $classAnnot['times']; $i++) {
            $entity = new $this->entityClassName();
            foreach ($propAnnot as $name => $MBRFProp) {
                $method = 'set'. ucfirst($name);

                // Generator is called according to the config mapping
                $generatorToCall = 'MikaelBrosset\RandomFixturesBundle\\' .  $this->ymlConfig['MBRFProp']['type']['generators'][$MBRFProp->getType()]['mapping'];
                $entity->$method((new $generatorToCall())->getValue($MBRFProp));
            }
            $ema->persist($entity);
        }
        $ema->flush();
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