<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Command;

use Doctrine\Common\Annotations\AnnotationReader;
use MikaelBrosset\RandomFixturesBundle\Generators;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

class PopulateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mb:rf:populate')
            ->setDescription('Populates random fixtures');
    }

    protected $mapping = [];

    function setMapping()
    {
        $this->mapping['firstname'] = new Generators\FirstNameGenerator();
        $this->mapping['lastname']  = new Generators\LastNameGenerator();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var EntityManager
         */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $this->setMapping();

        $userNb = 5;

        $reader = new AnnotationReader();

        $classNamespace = 'AppBundle\Entity\User'; /** @var TODO get files */
        $reflectClass = new \ReflectionClass(new $classNamespace());

        /*
         * Number of times a class will get fixtures
         */
        $times = $this->getTimes($reader, $reflectClass);

        /*
         * The annotations from properties
         */
        $propAnot = $this->readAnnotations($reader, $reflectClass);

        for ($i = 1; $i<= $times; $i++) {

            $user = new User(); /** @var TODO class */
            foreach ($propAnot as $name => $values) {

                $method = 'set'. ucfirst($name);
                $user->$method($this->mapping[$name]->getValue());
            }

            $em->persist($user);
        }

        $em->flush();
    }

    function getTimes(AnnotationReader $reader, $reflectClass)
    {
        $classAnnotation = $reader->getClassAnnotation($reflectClass, 'MikaelBrosset\RandomFixturesBundle\Annotation\MBRFClass');
        return $classAnnotation->times;
    }

    function readAnnotations(AnnotationReader $reader, $reflectClass)
    {
        $annotations = [];
        foreach ($reflectClass->getProperties() as $prop) {

           if (!is_null($reflProp = $reader->getPropertyAnnotation($prop, 'MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp'))) {
                $annotations[$prop->name] = [
                    'type'   => $reflProp->type,
                    'null'   => $reflProp->null,
                    'option' => $reflProp->option
                ];
           }
        }
        return $annotations;
    }
}