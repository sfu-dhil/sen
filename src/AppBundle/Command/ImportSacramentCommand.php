<?php

namespace AppBundle\Command;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventCategory;
use AppBundle\Entity\Location;
use AppBundle\Entity\LocationCategory;
use AppBundle\Entity\Person;
use AppBundle\Entity\Residence;
use AppBundle\Services\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function mb_convert_case;
use function mb_convert_encoding;

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

        $matches = array();
        if (preg_match("/(\d\d)\s*({$months})\s*(\d\d\d\d)/u", $string, $matches)) {
            $year = $matches[3];
            $month = sprintf("%02d", array_search($matches[2], self::MONTHS) + 1);
            $day = sprintf("%02d", $matches[1]);
            return "{$year}-{$month}-{$day}";
        } else if (preg_match("/({$months})\s*(\d\d\d\d)/u", $string, $matches)) {
            $year = $matches[2];
            $month = sprintf("%02d", array_search($matches[1], self::MONTHS) + 1);
            return "{$year}-{$month}-00";
        } else if (preg_match("/(\d\d\d\d)/u", $string, $matches)) {
            $year = $matches[1];
            return "{$year}-00-00";
        } else {
            print "UNPARSEABLE DATE: {$string}\n";
        }
        return null;
    }

    protected function findLocationCategory($name) {
        $category = $this->em->getRepository(LocationCategory::class)->findOneBy(array(
            'name' => $name,
        ));
        if (!$category) {
            $category = new LocationCategory();
            $category->setName($name);
            $category->setLabel(mb_convert_case($name, MB_CASE_TITLE));
            $this->em->persist($category);
        }
        return $category;
    }

    protected function findLocation($name, $categoryName) {
        if (!$name) {
            return;
        }
        $category = $this->findLocationCategory($categoryName);
        $location = $this->em->getRepository(Location::class)->findOneBy(array(
            'name' => $name,
            'category' => $category,
        ));
        if (!$location) {
            $location = new Location();
            $location->setName($name);
            $location->setCategory($category);
            $this->em->persist($location);
        }
        return $location;
    }

    protected function addManumission(Person $person, $row) {
        if (!$row[7]) {
            return;
        }
        $category = $this->em->getRepository(EventCategory::class)->findOneBy(array(
            'name' => 'manumission',
        ));
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setLocation($this->findLocation($row[6], 'church'));
        $this->em->persist($event);
    }

    protected function addBaptism(Person $person, $row) {
        if (!$row[5]) {
            return;
        }
        $category = $this->em->getRepository(EventCategory::class)->findOneBy(array(
            'name' => 'baptism',
        ));
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setLocation($this->findLocation($row[6], 'church'));
        $this->em->persist($event);
    }

    protected function addResidence(Person $person, $row) {
        if (!$row[10]) {
            return;
        }
        $city = $this->importer->findCity($row[10]);
        $residence = new Residence();
        $residence->setCity($city);
        $residence->setPerson($person);
        if ($row[9]) {
            $residence->setDate($row[9]);
        }
        $this->em->persist($residence);
    }

    protected function addAliases(Person $person, $row) {
        if (!$row[11]) {
            return;
        }
        $aliases = preg_split('/[,;]/', $row[11]);
        $person->setAlias($aliases);
    }

    protected function setNative(Person $person, $row) {
        if (!$row[12]) {
            return;
        }
        $person->setNative($row[12]);
    }

    protected function addOccupations(Person $person, $row) {
        if( ! $row[13]) {
            return;
        }
        $occupations = explode(';', $row[13]);
        $person->setOccupation($occupations);
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
            $person->setBirthDate($this->parseDate($row[2]));
            $person->setBirthPlace($this->importer->findCity($row[3]));
            $this->addBaptism($person, $row);
            $this->addManumission($person, $row);

            $this->addResidence($person, $row);
            $this->setNative($person, $row);
            $this->addAliases($person, $row);
            $this->addOccupations($person, $row);

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
