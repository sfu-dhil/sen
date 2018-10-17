<?php

namespace AppBundle\Command;

use AppBundle\Entity\Ledger;
use AppBundle\Entity\Notary;
use AppBundle\Entity\Person;
use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppImportNotaryCommand command.
 */
class ImportNotaryCommand extends ContainerAwareCommand {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, $name = null) {
        parent::__construct($name);
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Configure the command.
     */
    protected function configure() {
        $this
            ->setName('app:import:notary')
            ->setDescription('Import notarial data from one or more CSV files')
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of CSV files to import')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1)
        ;
    }

    protected function findNotary($name) {
        $repo = $this->em->getRepository(Notary::class);
        $notary = $repo->findOneBy(array(
            'name' => $name,
        ));
        if( ! $notary) {
            $notary = new Notary();
            $notary->setName($name);
            $this->em->persist($notary);
        }
        return $notary;
    }

    protected function findLedger(Notary $notary, $volume, $year) {
        $repo = $this->em->getRepository(Ledger::class);
        $ledger = $repo->findOneBy(array(
            'volume' => $volume,
            'notary' => $notary,
        ));
        if( ! $ledger) {
            $ledger = new Ledger();
            $ledger->setNotary($notary);
            $ledger->setVolume($volume);
            $ledger->setYear($year);
            $this->em->persist($ledger);
        }
        return $ledger;
    }

    protected function findRace($name) {
        if( ! $name) {
            return null;
        }
        $repo = $this->em->getRepository(Race::class);
        $race = $repo->findOneBy(array(
            'name' => $name,
        ));
        if( ! $race) {
            $race = new Race();
            $race->setName($name);
            $race->setLabel(ucwords($name));
            $this->em->persist($race);
        }
        return $race;
    }

    protected function findPerson($given, $family, $raceName, $status) {
        $repo = $this->em->getRepository(Person::class);
        $person = $repo->findOneBy(array(
            'firstName' => $given,
            'lastName' => $family,
        ));
        $race = $this->findRace($raceName);
        if( ! $person) {
            $person = new Person();
            $person->setFirstName($given);
            $person->setLastName($family);
            $person->setRace($race);
            $person->setStatus($status);
            $this->em->persist($person);
        }
        if($person->getRace() && $person->getRace()->getName() !== $raceName) {
            $this->logger->warn("Possible duplicate person: {$person} with races {$person->getRace()->getName()} and {$raceName}");
        }
        if($person->getStatus() !== $status) {
            $this->logger->warn("Possible duplicate person: {$person} with statuses {$person->getStatus()} and {$status}");
        }
        return $person;
    }

    protected function import($file, $skip) {
        $handle = fopen($file, 'r');
        for($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while($row = fgetcsv($handle)) {
            $notary = $this->findNotary($row[1]);
            $ledger = $this->findLedger($notary, $row[2], $row[3]);
            $firstParty = $this->findPerson($row[5], $row[4], $row[6], $row[7]);
            $secondParty = $this->findPerson($row[11], $row[12], $row[13], $row[14]);
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
        foreach($files as $file) {
            $this->import($file, $skip);
        }
    }

}
