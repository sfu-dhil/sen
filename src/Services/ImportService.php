<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Services;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Ledger;
use App\Entity\Location;
use App\Entity\LocationCategory;
use App\Entity\Notary;
use App\Entity\Person;
use App\Entity\Race;
use App\Entity\Relationship;
use App\Entity\RelationshipCategory;
use App\Entity\Residence;
use App\Entity\Transaction;
use App\Entity\TransactionCategory;
use App\Util\NotaryColumnDefinitions as N;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Description of AbstractImportService.
 *
 * @author michael
 */
class ImportService {
    public const MONTHS = [
        'jan', 'feb', 'mar', 'apr', 'may', 'jun',
        'jul', 'aug', 'sep', 'oct', 'nov', 'dec',
    ];

    public const CIRCAS = [
        'ca', 'bef', 'abt', 'aft',
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Construct the import service.
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Find a notary by name. Creates a new notary record if necessary.
     *
     * @param string $name
     *
     * @return Notary
     */
    public function findNotary($name) {
        $repo = $this->em->getRepository(Notary::class);
        $notary = $repo->findOneBy([
            'name' => $name,
        ]);
        if ( ! $notary) {
            $notary = new Notary();
            $notary->setName($name);
            $this->em->persist($notary);
        }

        return $notary;
    }

    /**
     * Find or create a ledger for a notary with the given volume identifier and
     * year.
     *
     * @param string $volume
     * @param int $year
     *
     * @return Ledger
     */
    public function findLedger(Notary $notary, $volume, $year) {
        $repo = $this->em->getRepository(Ledger::class);
        $ledger = $repo->findOneBy([
            'volume' => $volume,
            'notary' => $notary,
        ]);
        if ( ! $ledger) {
            $ledger = new Ledger();
            $ledger->setNotary($notary);
            $ledger->setVolume($volume);
            $ledger->setYear($year);
            $this->em->persist($ledger);
        }

        return $ledger;
    }

    /**
     * Find or create a race record by name.
     *
     * @param string $name
     *
     * @return Race
     */
    public function findRace($name) {
        if ( ! $name) {
            return;
        }
        $repo = $this->em->getRepository(Race::class);
        $race = $repo->findOneBy([
            'name' => $name,
        ]);
        if ( ! $race) {
            $race = new Race();
            $race->setName($name);
            $race->setLabel(ucwords($name));
            $this->em->persist($race);
        }

        return $race;
    }

    /**
     * Find or create a record for a person.
     *
     * @param string $given
     * @param string $family
     * @param string $raceName
     * @param string $status
     * @param mixed $sex
     *
     * @return Person
     */
    public function findPerson($given, $family, $raceName = '', $status = '', $sex = '') {
        $repo = $this->em->getRepository(Person::class);
        $person = $repo->findOneBy([
            'firstName' => $given,
            'lastName' => mb_convert_case($family, MB_CASE_TITLE),
        ]);
        $race = $this->findRace($raceName);
        $s = null;

        switch (mb_convert_case($sex, MB_CASE_LOWER)) {
            case 'male':
                $s = 'M';

                break;

            case 'female':
                $s = 'F';

                break;
        }
        if ( ! $person) {
            $person = new Person();
            $person->setFirstName($given);
            $person->setLastName($family);
            $person->setRace($race);
            $person->setStatus($status);
            $person->setSex($s);
            $this->em->persist($person);
        }
        if ($person->getRace() && $person->getRace()->getName() !== $raceName) {
            $this->logger->warn("Possible duplicate person: {$person} with races {$person->getRace()->getName()} and {$raceName}");
        }
        if ($person->getStatus() !== $status) {
            $this->logger->warn("Possible duplicate person: {$person} with statuses {$person->getStatus()} and {$status}");
        }

        return $person;
    }

    public function findCity($name) {
        $repo = $this->em->getRepository(City::class);
        $city = $repo->findOneBy([
            'name' => $name,
        ]);
        if ( ! $city) {
            $city = new City();
            $city->setName($name);
            $this->em->persist($city);
        }

        return $city;
    }

    /**
     * Find or create a transaction category by label.
     *
     * @param string $label
     *
     * @return TransactionCategory
     */
    public function findTransactionCategory($label) {
        $repo = $this->em->getRepository(TransactionCategory::class);
        $category = $repo->findOneBy([
            'label' => $label,
        ]);
        if ( ! $category) {
            $short = preg_replace('/[^a-z0-9]/u', '-', mb_convert_case($label, MB_CASE_LOWER));
            $category = new TransactionCategory();
            $category->setName($short);
            $category->setLabel($label);
            $this->em->persist($category);
        }

        return $category;
    }

    /**
     * Find or create a relationship category by name.
     *
     * @param string $name
     *
     * @return RelationshipCategory
     */
    public function findRelationshipCategory($name) {
        $repo = $this->em->getRepository(RelationshipCategory::class);
        $category = $repo->findOneBy([
            'name' => $name,
        ]);
        if ( ! $category) {
            $category = new RelationshipCategory();
            $category->setName($name);
            $category->setLabel(ucwords($name));
            $this->em->persist($category);
        }

        return $category;
    }

    /**
     * Create a transaction.
     *
     * @param array $row
     */
    public function createTransaction(Ledger $ledger, Person $firstParty, Person $secondParty, $row) {
        $transaction = new Transaction();
        $transaction->setLedger($ledger);
        $transaction->setFirstParty($firstParty);
        $transaction->setFirstPartyNote($row[N::first_party_notes]);
        if ($row[N::first_party_spouse]) {
            $firstSpouse = $this->findPerson($row[N::first_party_status], $firstParty->getLastName());
            $firstRelationship = new Relationship();
            $firstRelationship->setCategory($this->findRelationshipCategory('spouse'));
            $firstRelationship->setPerson($firstParty);
            $firstRelationship->setRelation($firstSpouse);
            $this->em->persist($firstRelationship);
        }
        $transaction->setConjunction($row[N::transaction_conjunction]);
        $transaction->setSecondParty($secondParty);
        if ($row[N::second_party_spouse]) {
            $secondSpouse = $this->findPerson($row[N::second_party_spouse], $secondParty->getLastName());
            $secondRelationship = new Relationship();
            $secondRelationship->setCategory($this->findRelationshipCategory('spouse'));
            $secondRelationship->setPerson($secondParty);
            $secondRelationship->setRelation($secondSpouse);
            $this->em->persist($secondRelationship);
        }
        $transaction->setSecondPartyNote($row[N::second_party_notes]);
        $transaction->setCategory($this->findTransactionCategory($row[N::transaction_category]));
        $date = new DateTimeImmutable($row[N::transaction_date]);
        $transaction->setDate($date);
        $transaction->setPage($row[N::ledger_page]);
        $transaction->setNotes($row[N::transaction_notes]);
        $this->em->persist($transaction);

        return $transaction;
    }

    public function parseDate($string) {
        static $months = null;
        if ( ! $months) {
            $months = implode('|', self::MONTHS);
        }
        static $circas = null;
        if ( ! $circas) {
            $circas = implode('|', self::CIRCAS);
        }

        if ( ! $string) {
            return;
        }
        $string = mb_convert_case($string, MB_CASE_LOWER);
        $matches = [];
        if (preg_match("/(\\d{1,2})\\s*({$months})\\s*(\\d\\d\\d\\d)/u", $string, $matches)) {
            $year = $matches[3];
            $month = sprintf('%02d', array_search($matches[2], self::MONTHS, true) + 1);
            $day = sprintf('%02d', $matches[1]);

            return "{$year}-{$month}-{$day}";
        }
        if (preg_match("/({$months})\\s*(\\d\\d\\d\\d)/u", $string, $matches)) {
            $year = $matches[2];
            $month = sprintf('%02d', array_search($matches[1], self::MONTHS, true) + 1);

            return "{$year}-{$month}-00";
        }
        if (preg_match('/(\\d\\d\\d\\d)/u', $string, $matches)) {
            $year = $matches[1];

            return "{$year}-00-00";
        }
        $this->logger->error("UNPARSEABLE DATE: {$string}");
    }

    public function findLocationCategory($name) {
        $category = $this->em->getRepository(LocationCategory::class)->findOneBy([
            'name' => $name,
        ]);
        if ( ! $category) {
            $category = new LocationCategory();
            $category->setName($name);
            $category->setLabel(mb_convert_case($name, MB_CASE_TITLE));
            $this->em->persist($category);
        }

        return $category;
    }

    public function findLocation($name, $categoryName) {
        if ( ! $name) {
            return;
        }
        $category = $this->findLocationCategory($categoryName);
        $location = $this->em->getRepository(Location::class)->findOneBy([
            'name' => $name,
            'category' => $category,
        ]);
        if ( ! $location) {
            $location = new Location();
            $location->setName($name);
            $location->setCategory($category);
            $this->em->persist($location);
        }

        return $location;
    }

    public function addManumission(Person $person, $row) {
        if ( ! isset($row[7]) || ! $row[7]) {
            return;
        }
        $category = $this->em->getRepository(EventCategory::class)->findOneBy([
            'name' => 'manumission',
        ]);
        if ( ! $category) {
            throw new Exception('Manumission event category is missing.');
        }
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setDate($this->parseDate($row[7]));
        $event->setWrittenDate($row[7]);
        if (isset($row[8]) && $row[8]) {
            $event->setLocation($this->findLocation($row[8], ''));
        }
        $this->em->persist($event);

        return $event;
    }

    public function addBaptism(Person $person, $row) {
        if ( ! isset($row[5]) || ! $row[5]) {
            return;
        }
        $category = $this->em->getRepository(EventCategory::class)->findOneBy([
            'name' => 'baptism',
        ]);
        if ( ! $category) {
            throw new Exception('Baptism event category is missing.');
        }
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setDate($this->parseDate($row[5]));
        $event->setWrittenDate($row[5]);
        if (isset($row[6]) && $row[6]) {
            $event->setLocation($this->findLocation($row[6], 'church'));
        }
        $this->em->persist($event);

        return $event;
    }

    public function addResidence(Person $person, $row) : void {
        if ( ! isset($row[10]) || ! $row[10]) {
            return;
        }
        $city = $this->findCity($row[10]);
        $residence = new Residence();
        $residence->setCity($city);
        $residence->setPerson($person);
        $person->addResidence($residence);
        if (isset($row[9]) && $row[9]) {
            $residence->setDate($row[9]);
        }
        $this->em->persist($residence);
    }

    public function addAliases(Person $person, $row) : void {
        if ( ! isset($row[11]) || ! $row[11]) {
            return;
        }
        $aliases = preg_split('/[,;]/', $row[11]);
        $person->addAlias($aliases);
    }

    public function setNative(Person $person, $row) : void {
        if ( ! isset($row[12]) || ! $row[12]) {
            return;
        }
        $person->setNative($row[12]);
    }

    public function addOccupations(Person $person, $row) : void {
        if ( ! isset($row[13]) || ! $row[13]) {
            return;
        }
        $occupations = explode(';', $row[13]);
        $person->addOccupation($occupations);
    }
}
