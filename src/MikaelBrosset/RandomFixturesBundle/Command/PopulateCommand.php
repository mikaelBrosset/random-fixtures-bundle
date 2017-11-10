<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Command;

use MikaelBrosset\RandomFixturesBundle\Service\AnnotationManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $projectDir = $this->getContainer()->get('kernel')->getProjectDir(); // To be configured as service
        $em = $this->getContainer()->get('doctrine')->getManager(); // To be configured as service

        (new AnnotationManager($output, $projectDir, $em))->manage();
    }
}