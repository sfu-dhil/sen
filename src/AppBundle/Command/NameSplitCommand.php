<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppImportNotaryCommand command.
 */
class NameSplitCommand extends ContainerAwareCommand {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /**
     * Configure the command.
     */
    protected function configure() {
        $this
            ->setName('app:name:split')
            ->setDescription('Split names in CSV files.')
            ->addArgument('file', InputArgument::REQUIRED, 'File with columns to split')
            ->addArgument('cols', InputArgument::IS_ARRAY, 'List of column numbers to split.')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1)
        ;
    }

    protected function split($file, $cols, $skip) {
        $handle = fopen($file, 'r');
        $stdout = fopen('php://stdout', 'w');
        
        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {            
            $new = array();
            foreach($row as $i => $data) {
                $new[] = $data;
                if(in_array($i, $cols)) {
                    $matches = array();
                    if(preg_match("/(.*?)\s([[:upper:] ]+)$/u", $data, $matches)) {
                        $new[] = $matches[1]; 
                        $new[] = $matches[2];
                    } else {
                        $new[] = '';
                        $new[] = '';
                    }
                }
            }
            fputcsv($stdout, $new);
        }
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     *   Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *   Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $file = $input->getArgument('file');
        $cols = $input->getArgument('cols');
        $skip = $input->getOption('skip');
        $this->split($file, $cols, $skip);
    }

}
