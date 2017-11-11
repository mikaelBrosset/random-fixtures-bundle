<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 09/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Output\OutputInterface;
use MikaelBrosset\RandomFixturesBundle\Generators;

abstract class Manager
{
    protected $mapping = [];
    protected $output;
    protected $em;

    public function __construct(OutputInterface $output, $projectDir, EntityManager $em, $dir = '/src/')
    {
        $this->output = $output;
        $this->em = $em;
        $this->absDir = $projectDir . $dir;
        $this->reader = new AnnotationReader();
    }

    /**
     * Maps the annotation name with their Generator
     */
    function setNameMapping()
    {
        $this->mapping['firstname']       = new Generators\FirstnameGenerator();
        $this->mapping['femalefirstname'] = new Generators\FemaleFirstnameGenerator();
        $this->mapping['malefirstname']   = new Generators\MaleFirstnameGenerator();
        $this->mapping['lastname']        = new Generators\LastnameGenerator();
    }

    public function manage()
    {
        $rit = $this->findFiles($this->absDir);
        $this->setNameMapping();

        foreach ($rit as $r) {
            $this->manageAnnotation($r);
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