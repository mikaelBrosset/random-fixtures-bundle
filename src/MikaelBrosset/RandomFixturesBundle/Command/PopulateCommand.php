<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Command;

use MikaelBrosset\RandomFixturesBundle\Service\EntityAnnotationManager;
use MikaelBrosset\RandomFixturesBundle\Service\MBRFManager;
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
        ini_set('xdebug.var_display_max_depth', 10);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);

        $projectDir = $this->getContainer()->get('kernel')->getProjectDir(); // To be configured as service
        $em = $this->getContainer()->get('doctrine')->getManager(); // To be configured as service

        $results = (new MBRFManager($output, $projectDir, $em))->manage();

        $output->writeln("<info>All Done<info>");
    }
}