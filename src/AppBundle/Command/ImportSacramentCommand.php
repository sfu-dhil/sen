<?php

namespace AppBundle\Command;

use AppBundle\Services\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppImportSacramentCommand command.
 */
class ImportSacramentCommand extends ContainerAwareCommand {

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
    protected function configure() {
        $this
            ->setName('app:import:sacrament')
            ->setDescription('Import sacramental data from one or more CSV files')
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of CSV files to import')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1)
        ;
    }

    protected function import($file, $skip) {
        $handle = fopen($file, 'r');
        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {
            $row = array_map(function($data) {
                return mb_convert_encoding($data, "UTF-8", "UTF-8");
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
     *   Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *   Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $files = $input->getArgument('files');
        $skip = $input->getOption('skip');
        foreach ($files as $file) {
            $this->import($file, $skip);
        }
    }

}
