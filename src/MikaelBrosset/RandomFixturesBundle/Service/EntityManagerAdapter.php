<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 17/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Service;

use Doctrine\ORM\EntityManager;

class EntityManagerAdapter
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function persist($data)
    {
        $this->em->persist($data);
    }

    public function flush()
    {
        $this->em->flush();
        $this->em->clear();
    }
}
