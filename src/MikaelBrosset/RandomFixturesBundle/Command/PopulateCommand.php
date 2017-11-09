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
use Symfony\Component\Finder\Finder;

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
        $this->mapping['firstname']       = new Generators\FirstNameGenerator();
        $this->mapping['femalefirstname'] = new Generators\FemaleFirstNameGenerator();
        $this->mapping['malefirstname']   = new Generators\MaleFirstNameGenerator();
        $this->mapping['lastname']        = new Generators\LastNameGenerator();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = '/src/';
        $absDir = $this->getContainer()->get('kernel')->getProjectDir() . $dir;

        $finder = new Finder();
        $finder->files()
            ->in($absDir);

        $it = $finder->files()->getIterator();

        $rit = new \RegexIterator($it, "#Entity\/\w+\/*\.php$#");
        
        if (is_null(!$rit->valid())) {
            $output->writeln(sprintf("<error>No php Entity found in %s<error>", $absDir));
            exit();
        }

        foreach ($rit as $r) {
            $this->manager($r, $output);
        }
    }

    function manager($file, OutputInterface $output)
    {
        $this->setMapping();
        $classNamespace = 'AppBundle\Entity\User'; /** @var TODO get files */
        $reflectClass = new \ReflectionClass(new $classNamespace());
        $reader = new AnnotationReader();
        $em = $this->getContainer()->get('doctrine')->getManager();


        /*
         * Number of times a class will get fixtures
         */
        if (!$times = $this->getTimes($reader, $reflectClass)) {
            $output->writeln(sprintf("<error>No Class annotation found for %s<error>", $classNamespace));
        }

        /*
         * The annotations from properties
         */
        $propAnot = $this->readAnnotations($reader, $reflectClass);

        for ($i = 1; $i<= $times; $i++) {

            $user = new User(); /** @var TODO class */
            foreach ($propAnot as $name => $values) {
                $method = 'set'. ucfirst($name);
                $user->$method($this->mapping[$values['type']]->getValue($values['type'], $values['option']));
            }

            $em->persist($user);
        }
        $em->flush();
        $em->clear();
    }

    function getTimes(AnnotationReader $reader, $reflectClass)
    {
        if (is_null($classAnnotation = $reader->getClassAnnotation($reflectClass, 'MikaelBrosset\RandomFixturesBundle\Annotation\MBRFClass'))) {
            return false;
        }
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