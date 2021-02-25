<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Services\ImportService;
use App\Util\ColumnDefinitions as D;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppImportNotaryCommand command.
 */
class ImportNotaryCommand extends Command {
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
            ->setName('app:import:notary')
            ->setDescription('Import notarial data from one or more CSV files')
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of CSV files to import')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1)
        ;
    }

    /**
     * @param $file
     * @param $skip
     *
     * @throws Exception
     */
    protected function import($file, $skip) : void {
        $handle = fopen($file, 'r');

        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {
            $date = new DateTimeImmutable($row[D::transaction_date]);
            $notary = $this->importer->findNotary($row[D::notary_name]);
            $ledger = $this->importer->findLedger($notary, $row[D::ledger_volume], $date->format('Y'));

            $firstParty = $this->importer->findPerson(
                $row[D::first_party_first_name],
                $row[D::first_party_last_name],
                $row[D::first_party_race],
                $row[D::first_party_status]
            );

            $secondParty = $this->importer->findPerson(
                $row[D::second_party_first_name],
                $row[D::second_party_last_name],
                $row[D::second_party_race],
                $row[D::second_party_status]
            );

            $transaction = $this->importer->createTransaction($ledger, $firstParty, $secondParty, $row);
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
