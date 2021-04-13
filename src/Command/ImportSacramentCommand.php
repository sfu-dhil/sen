<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Services\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppImportSacramentCommand command.
 */
class ImportSacramentCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ImportService
     */
    private $importer;

    public function __construct(EntityManagerInterface $em, ImportService $importer, $name = null) {
        parent::__construct($name);
        $this->importer = $importer;
        $this->em = $em;
    }

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this
            ->setName('app:import:sacrament')
            ->setDescription('Import sacramental data from one or more CSV files')
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of CSV files to import')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1)
        ;
    }

    protected function import($file, $skip) : void {
        $handle = fopen($file, 'r');

        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {
            $row = array_map(function ($data) {
                return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }, $row);
            $person = $this->importer->findPerson($row[0], $row[1]);
            $person->setBirthDateDisplay($row[2]);
            $person->setBirthDate($this->importer->parseDate($row[2]));
            $person->setBirthPlace($this->importer->findCity($row[3]));
            $this->importer->addBaptism($person, $row);
            $this->importer->addManumission($person, $row);

            $this->importer->addResidence($person, $row);
            $this->importer->setNative($person, $row);
            $this->importer->addAliases($person, $row);
            $this->importer->addOccupations($person, $row);

            $this->em->flush();
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
        $files = $input->getArgument('files');
        $skip = $input->getOption('skip');

        foreach ($files as $file) {
            $this->import($file, $skip);
        }
    }
}
