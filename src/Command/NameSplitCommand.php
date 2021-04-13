<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppImportNotaryCommand command.
 */
class NameSplitCommand extends Command
{
    public function __construct($name = null) {
        parent::__construct($name);
    }

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this
            ->setName('app:name:split')
            ->setDescription('Split names in CSV files.')
            ->addArgument('file', InputArgument::REQUIRED, 'File with columns to split')
            ->addArgument('cols', InputArgument::IS_ARRAY, 'List of column numbers to split.')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1)
        ;
    }

    protected function split($file, $cols, $skip) : void {
        $handle = fopen($file, 'r');
        $stdout = fopen('php://stdout', 'w');

        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {
            $new = [];

            foreach ($row as $i => $data) {
                $new[] = $data;
                if (in_array($i, $cols, true)) {
                    $matches = [];
                    if (preg_match('/(.*?)\\s([[:upper:] ]+)$/u', $data, $matches)) {
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
     *                              Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *                                Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $file = $input->getArgument('file');
        $cols = $input->getArgument('cols');
        $skip = $input->getOption('skip');
        $this->split($file, $cols, $skip);
    }
}
