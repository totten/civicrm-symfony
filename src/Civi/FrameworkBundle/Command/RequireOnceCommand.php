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
        /*
        global $civicrm_root;
        $finder = new Finder();
        $finder->name('*.php');
        foreach ($finder->in("$civicrm_root/CRM") as $file) {
          print "[$file]\n";
          require_once $file;
        }
        */
        $file = $input->getArgument("file");
        try {
            require_once $file;
        } catch (\ErrorException $e) {
            print "\nFILE: $file\n";
            $output->writeln($e->getMessage());
        }

    }
}
