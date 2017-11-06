<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use MikaelBrosset\RandomFixturesBundle\Lists;
use AppBundle\Entity\User;

class PopulateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mb:rf:populate')
            ->setDescription('Populates random fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var EntityManager
         */
        $em = $this->getContainer()->get('doctrine')->getManager();


        $userNb = 5;

        for ($i = 1; $i<= $userNb; $i++) {
            $firstName = (new Lists\MaleFirstNames())->getRandomMaleFirstName();
            $lastName  = (new Lists\LastNames())->getRandomLastName();

            $user = (new User())
                ->setFirstName($firstName)
                ->setLastName($lastName);
            //echo $firstName . '-' .$lastName;

            $em->persist($user);
        }

        $em->flush();
    }
}