<?php

namespace AppBundle\Command;

use AppBundle\Services\ImportService;
use DateTime;
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

    const MONTHS = array(
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
        'Dec'
    );

    const CIRCAS = array(
        'ca', 'bef', 'abt', 'aft'
    );

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

    protected function parseDate($string) {
        static $months = null;
        if (!$months) {
            $months = implode('|', self::MONTHS);
        }
        static $circas = null;
        if (!$circas) {
            $circas = implode('|', self::CIRCAS);
        }

        if (!$string) {
            return null;
        }

        print "\n\n$string\n";
        $matches = array();
        if (preg_match("/(\d\d)\s*({$months})\s*(\d\d\d\d)/", $string, $matches)) {
            $year = $matches[3];
            $month = sprintf("%02d", array_search($matches[2], self::MONTHS) + 1);
            $day = sprintf("%02d", $matches[1]);
            return "{$year}-{$month}-{$day}";
        } else if (preg_match("/({$months})\s*(\d\d\d\d)/", $string, $matches)) {
            $year = $matches[2];
            $month = sprintf("%02d", array_search($matches[1], self::MONTHS) + 1);
            return "{$year}-{$month}-00";
        } else if (preg_match("/(\d\d\d\d)/", $string, $matches)) {
            $year = $matches[1];
            return "{$year}-00-00";
        } else {
            print "UNPARSEABLE DATE: {$string}\n";
        }
        return null;
    }

    protected function import($file, $skip) {
        $handle = fopen($file, 'r');
        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {
            $person = $this->importer->findPerson($row[0], $row[1]);
            $person->setBirthDateDisplay($row[2]);
            $person->setBirthDate($this->parseDate($row[2]));
            $person->setBirthPlace($this->importer->findCity($row[3]));
            // $this->em->flush();
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
