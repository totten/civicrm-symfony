<?php
namespace Civi\FrameworkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Civi\FrameworkBundle\CiviCRM;
use Symfony\Component\Finder\Finder;

class RequireOnceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('civicrm:require-once')
            ->setDescription('Simply load a PHP file and check for syntax errors (using whatever options are active in Symfony)')
            ->addArgument("file", InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $civicrm = $this->getContainer()->get('civi_framework.civicrm');
        $file = $input->getArgument("file");
        try {
            require_once $file;
        } catch (\ErrorException $e) {
            $output->writeln("\nFILE: $file");
            $output->writeln($e->getMessage());
        }

    }
}
