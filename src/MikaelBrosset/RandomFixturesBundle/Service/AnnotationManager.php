<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use AppBundle\Entity\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

class AnnotationManager extends EntityFinder
{
    protected $reader;

    /**
     * Manage Annotation for One Entity
     */
    protected function manageAnnotation(SplFileInfo $file, OutputInterface $output)
    {
        $entity = $this->loadClassFromNamespace($file);
        $r = new \ReflectionClass(new $entity());

        //Number of times a class will be copied in db
        if (!$times = $this->getTimes($this->reader, $r)) {
            $output->writeln(sprintf("<error>No Class annotation found for %s<error>", $classNamespace));
        }

        //The annotations coming from entity properties
        $propAnot = $this->readAnnotations($this->reader, $r);

        for ($i = 1; $i<= $times; $i++) {

            $user = new User(); /** @var TODO class */
            foreach ($propAnot as $name => $values) {
                $method = 'set'. ucfirst($name);
                $user->$method($this->mapping[$values['type']]->getValue($values['type'], $values['option']));
            }

            $this->persist($user);
        }
        $this->flush();
    }

    /**
     * Get the number of times an Entity has to be copied in the database
     */
    private function getTimes(AnnotationReader $reader, $reflectClass)
    {
        if (is_null($classAnnot = $reader->getClassAnnotation($reflectClass, 'MikaelBrosset\RandomFixturesBundle\Annotation\MBRFClass'))) {
            return false;
        }
        return $classAnnot->times;
    }

    private function readAnnotations(AnnotationReader $reader, $reflectClass): array
    {
        $annot = [];
        foreach ($reflectClass->getProperties() as $prop) {

            if (!is_null($reflProp = $reader->getPropertyAnnotation($prop, 'MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp'))) {
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