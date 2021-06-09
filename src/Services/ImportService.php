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
use App\Entity\Witness;
use App\Repository\BirthStatusRepository;
use App\Repository\CityRepository;
use App\Repository\EventCategoryRepository;
use App\Repository\LedgerRepository;
use App\Repository\LocationCategoryRepository;
use App\Repository\LocationRepository;
use App\Repository\NotaryRepository;
use App\Repository\PersonRepository;
use App\Repository\RaceRepository;
use App\Repository\RelationshipCategoryRepository;
use App\Repository\TransactionCategoryRepository;
use App\Repository\WitnessCategoryRepository;
use App\Util\NotaryColumnDefinitions as N;
use App\Util\SacramentColumnDefinitions as S;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Description of AbstractImportService.
 */
class ImportService {
    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    private NotaryRepository $notaryRepository;

    private LedgerRepository $ledgerRepository;

    private RaceRepository $raceRepository;

    private PersonRepository $personRepository;

    private CityRepository $cityRepository;

    private TransactionCategoryRepository $transactionCategoryRepository;

    private RelationshipCategoryRepository $relationshipCategoryRepository;

    private LocationCategoryRepository $locationCategoryRepository;

    private LocationRepository $locationRepository;

    private EventCategoryRepository $eventCategoryRepository;

    private BirthStatusRepository $birthStatusRepository;

    private WitnessCategoryRepository $witnessCategoryRepository;

    /**
     * Construct the import service.
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Find a notary by name. Creates a new notary record if necessary.
     */
    public function findNotary(string $name) : Notary {
        $notary = $this->notaryRepository->findOneBy([
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
     */
    public function findLedger(Notary $notary, string $volume, int $year) : Ledger {
        $ledger = $this->ledgerRepository->findOneBy([
            'volume' => $volume,
            'notary' => $notary,
        ]);
        if ( ! $ledger) {
            $ledger = new Ledger();
            $ledger->setNotary($notary);
            $ledger->setVolume($volume);
            $ledger->setYear((int) $year);
            $this->em->persist($ledger);
        }

        return $ledger;
    }

    /**
     * Find or create a race record by name.
     *
     * @return ?Race
     */
    public function findRace(?string $name) {
        $this->logger->warning('ImportService->findRace() needs to be rewritten.');
        if ( ! $name) {
            return null;
        }
        $race = $this->raceRepository->findOneBy([
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
     * @param mixed $sex
     */
    public function findPerson(?string $given, ?string $family, ?string $raceName = '', $sex = '') : Person {
        $normGiven = mb_convert_case($given, \MB_CASE_TITLE);
        $normFamily = mb_convert_case($family, \MB_CASE_TITLE);
        $person = $this->personRepository->findOneBy([
            'firstName' => $normGiven,
            'lastName' => $normFamily,
        ]);
        $race = $this->findRace($raceName);
        if ($person) {
            if ($person->getRace() && $person->getRace()->getName() !== $raceName) {
                $this->logger->warn("Possible duplicate person: {$person} with races {$person->getRace()->getName()} and {$raceName}");
            }
        } else {
            $person = new Person();
            $person->setFirstName($normGiven);
            $person->setLastName($normFamily);
            $person->setRace($race);
            if ($sex) {
                $person->setSex($sex[0]);
            }
            $this->em->persist($person);
        }

        return $person;
    }

    public function findCity($name) : City {
        $city = $this->cityRepository->findOneBy([
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
     */
    public function findTransactionCategory(string $label) : TransactionCategory {
        $category = $this->transactionCategoryRepository->findOneBy([
            'label' => $label,
        ]);
        if ( ! $category) {
            $short = preg_replace('/[^a-z0-9]/u', '-', mb_convert_case($label, \MB_CASE_LOWER));
            $category = new TransactionCategory();
            $category->setName($short);
            $category->setLabel($label);
            $this->em->persist($category);
        }

        return $category;
    }

    /**
     * Find or create a relationship category by name.
     */
    public function findRelationshipCategory(string $name) : RelationshipCategory {
        $category = $this->relationshipCategoryRepository->findOneBy([
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
     */
    public function createTransaction(Ledger $ledger, Person $firstParty, Person $secondParty, array $row) {
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
        $date = new DateTime($row[N::transaction_date]);
        $transaction->setDate($date);
        $transaction->setPage((int) $row[N::ledger_page]);
        $transaction->setNotes($row[N::transaction_notes]);
        $this->em->persist($transaction);

        return $transaction;
    }

    public function parseDate($string) : ?string {
        $string = preg_replace('/(^\\s*)|(\\s*$)/', '', $string);
        if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $string)) {
            return $string;
        }
        if (preg_match('/^\\d{4}-\\d{2}$/', $string)) {
            return $string;
        }
        if (preg_match('/^\\d{4}$/', $string)) {
            return $string;
        }
        $this->logger->error("UNPARSEABLE DATE: {$string}");

        return null;
    }

    public function findLocationCategory($name) : LocationCategory {
        $category = $this->locationCategoryRepository->findOneBy([
            'name' => $name,
        ]);
        if ( ! $category) {
            $category = new LocationCategory();
            $category->setName($name);
            $category->setLabel(mb_convert_case($name, \MB_CASE_TITLE));
            $this->em->persist($category);
        }

        return $category;
    }

    public function findLocation($name, $categoryName = 'city') : ?Location {
        if ( ! $name) {
            return null;
        }
        $category = $this->findLocationCategory($categoryName);
        $location = $this->locationRepository->findOneBy([
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

    public function addManumission(Person $person, $row, $name = 'manumission') : ?Event {
        if ( ! isset($row[S::manumission_date]) || ! $row[S::manumission_date]) {
            return null;
        }
        $category = $this->eventCategoryRepository->findOneBy([
            'name' => $name,
        ]);
        if ( ! $category) {
            throw new Exception('Manumission event category is missing.');
        }
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setDate($this->parseDate($row[S::manumission_date]));
        $event->setWrittenDate($row[S::manumission_date_written]);
        if (isset($row[S::manumission_place]) && $row[S::manumission_place]) {
            $event->setLocation($this->findLocation($row[S::manumission_place], ''));
        }
        $this->em->persist($event);

        return $event;
    }

    public function addBaptism(Person $person, $row, $name = 'baptism') : ?Event {
        if ( ! isset($row[S::event_baptism_place]) || ! $row[S::event_baptism_place]) {
            return null;
        }
        $category = $this->eventCategoryRepository->findOneBy([
            'name' => $name,
        ]);
        if ( ! $category) {
            throw new Exception('Baptism event category is missing.');
        }
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setDate($this->parseDate($row[S::event_baptism_date]));
        $event->setWrittenDate($row[S::event_written_baptism_date]);
        $event->setLocation($this->findLocation($row[S::event_baptism_place], 'church'));
        $event->setRecordSource($row[S::event_baptism_source]);
        $this->em->persist($event);

        return $event;
    }

    public function addAliases(Person $person, $row) : void {
        if ( ! isset($row[S::alias]) || ! $row[S::alias]) {
            return;
        }
        $aliases = preg_split('/[;]/', $row[S::alias]);
        $person->setAliases(array_merge($person->getAliases(), $aliases));
    }

    public function setNative(Person $person, $row) : void {
        if ( ! isset($row[S::native]) || ! $row[S::native]) {
            return;
        }
        $person->setNative($row[S::native]);
    }

    public function addOccupations(Person $person, $row) : void {
        if ( ! isset($row[S::occupation]) || ! $row[S::occupation]) {
            return;
        }
        $occupations = [];
        $list = explode(';', $row[13]);
        foreach ($list as $data) {
            $m = [];
            if (preg_match('/^(\d{4})\s*(.*)\s*$/u', $data, $m)) {
                $occupations[] = ['date' => $m[1], 'occupation' => $m[2]];
            } else {
                $occupations[] = $data;
            }
        }
        $person->setOccupations(array_merge($person->getOccupations(), $occupations));
    }

    public function setWrittenRace(Person $person, array $row) : void {
        if ( ! isset($row[S::written_race]) || ! $row[S::written_race]) {
            return;
        }

        $races = array_map(static fn($s) => trim($s), explode(';', $row[S::written_race]));
        $person->setWrittenRaces(array_merge($person->getWrittenRaces(), $races));
    }

    public function setStatus(Person $person, array $row) : void {
        if ( ! isset($row[S::status]) || ! $row[S::status]) {
            return;
        }
        $person->setStatuses(array_merge($person->getStatuses(), [$row[S::status]]));
    }

    public function addBirth(Person $person, array $row) : ?Event {
        if ( ! isset($row[S::birth_date]) || ! $row[S::birth_date]) {
            return null;
        }

        $category = $this->eventCategoryRepository->findOneBy(['name' => 'birth']);
        if ( ! $category) {
            throw new Exception('Birth event category is missing.');
        }
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setDate($this->parseDate($row[S::birth_date]));
        $event->setWrittenDate($row[S::written_birth_date]);
        $event->setLocation($this->findLocation($row[S::birth_place]));
        $this->em->persist($event);

        return $event;
    }

    public function setBirthStatus(Person $person, array $row) : void {
        if ( ! isset($row[S::birth_status]) || ! $row[S::birth_status]) {
            return;
        }
        $status = $this->birthStatusRepository->findOneBy(['name' => $row[S::birth_status]]);
        if ( ! $status) {
            throw new Exception('Birth status record is missing for ' . $row[S::birth_status]);
        }
        $person->setBirthStatus($status);
    }

    /**
     * @throws Exception
     */
    public function addParents(Person $person, array $row) : void {
        if ($row[S::father_first_name] || $row[S::father_last_name]) {
            $father = $this->findPerson($row[S::father_first_name], $row[S::father_last_name], null, 'Male');
            $fatherCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'father']);
            if ( ! $fatherCategory) {
                throw new Exception("Relationship category 'father' is missing.");
            }
            $relationship = new Relationship();
            $relationship->setPerson($person);
            $relationship->setRelation($father);
            $relationship->setCategory($fatherCategory);
            $this->em->persist($relationship);

            $childCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'child']);
            if ( ! $childCategory) {
                throw new Exception("Relationship category 'child' is missing.");
            }
            $relation = new Relationship();
            $relation->setPerson($father);
            $relation->setRelation($person);
            $relation->setCategory($childCategory);
            $this->em->persist($relation);
        }

        if ($row[S::mother_first_name] || $row[S::mother_last_name]) {
            $mother = $this->findPerson($row[S::mother_first_name], $row[S::mother_last_name], null, 'Male');
            $motherCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'mother']);
            if ( ! $motherCategory) {
                throw new Exception("Relationship category 'mother' is missing.");
            }
            $relationship = new Relationship();
            $relationship->setPerson($person);
            $relationship->setRelation($mother);
            $relationship->setCategory($motherCategory);
            $this->em->persist($relationship);

            $childCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'child']);
            if ( ! $childCategory) {
                throw new Exception("Relationship category 'child' is missing.");
            }
            $relation = new Relationship();
            $relation->setPerson($mother);
            $relation->setRelation($person);
            $relation->setCategory($childCategory);
            $this->em->persist($relation);
        }
    }

    /**
     * @throws Exception
     */
    public function addGodParents(Person $person, array $row, Event $baptism) : void {
        if ($row[S::godfather_first_name] || $row[S::godfather_last_name]) {
            $godfather = $this->findPerson($row[S::godfather_first_name], $row[S::godfather_last_name], null, 'Male');
            $godfatherCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'godfather']);
            if ( ! $godfatherCategory) {
                throw new Exception("Relationship category 'godfather' is missing.");
            }
            $relationship = new Relationship();
            $relationship->setPerson($person);
            $relationship->setRelation($godfather);
            $relationship->setCategory($godfatherCategory);
            $this->em->persist($relationship);

            $childCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'child']);
            if ( ! $childCategory) {
                throw new Exception("Relationship category 'godchild' is missing.");
            }
            $relation = new Relationship();
            $relation->setPerson($godfather);
            $relation->setRelation($person);
            $relation->setCategory($childCategory);
            $this->em->persist($relation);

            $witness = new Witness();
            $category = $this->witnessCategoryRepository->findOneBy(['name' => 'godparent']);
            if ( ! $category) {
                throw new Exception("Witness category 'godparent' is missing.");
            }
            $witness->setCategory($category);
            $witness->setPerson($godfather);
            $witness->setEvent($baptism);
        }

        if ($row[S::godmother_first_name] || $row[S::godmother_last_name]) {
            $godmother = $this->findPerson($row[S::godmother_first_name], $row[S::godmother_last_name], null, 'Male');
            $godmotherCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'godmother']);
            if ( ! $godmotherCategory) {
                throw new Exception("Relationship category 'godmother' is missing.");
            }
            $relationship = new Relationship();
            $relationship->setPerson($person);
            $relationship->setRelation($godmother);
            $relationship->setCategory($godmotherCategory);
            $this->em->persist($relationship);

            $childCategory = $this->relationshipCategoryRepository->findOneBy(['name' => 'child']);
            if ( ! $childCategory) {
                throw new Exception("Relationship category 'godchild' is missing.");
            }
            $relation = new Relationship();
            $relation->setPerson($godmother);
            $relation->setRelation($person);
            $relation->setCategory($childCategory);
            $this->em->persist($relation);
        }
    }

    public function addMarriage(Person $person, array $row) : Event {
        return new Event();
    }

    /**
     * @throws Exception
     */
    public function addSpouse(Person $person, array $row) : void {
        if ($row[S::spouse_first_name] || $row[S::spouse_last_name]) {
            $spouse = $this->findPerson($row[S::spouse_first_name], $row[S::spouse_last_name]);
            $category = $this->relationshipCategoryRepository->findOneBy(['name' => 'spouse']);
            if ( ! $category) {
                throw new Exception("Relationship category 'spouse' is missing.");
            }
            $relationship = new Relationship();
            $relationship->setPerson($person);
            $relationship->setCategory($category);
            $relationship->setRelation($spouse);
            $this->em->persist($relationship);

            $relation = new Relationship();
            $relation->setPerson($spouse);
            $relation->setCategory($category);
            $relation->setRelation($person);
            $this->em->persist($relation);
        }
    }

    public function addMarriageWitnesses(Person $person, array $row, Event $marriage) : void {
    }

    public function addDeath(Person $person, array $row) : ?Event {
        if ( ! isset($row[S::event_death_date]) || ! $row[S::event_death_date]) {
            return null;
        }

        $category = $this->eventCategoryRepository->findOneBy(['name' => 'death']);
        if ( ! $category) {
            throw new Exception('Death event category is missing.');
        }
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setDate($this->parseDate($row[S::event_death_date]));
        $event->setWrittenDate($row[S::event_written_death_date]);
        $event->setLocation($this->findLocation($row[S::event_death_place]));
        $this->em->persist($event);

        return $event;
    }

    public function addResidences(Person $person, array $row) : void {
        if ( ! $row[S::residence_dates] || ! $row[S::residence_places]) {
            return;
        }
        $dates = array_map(static fn($d) => preg_replace('/^\\s+|\\s+$/u', '', $d), explode(';', $row[S::residence_dates]));
        $addresses = array_map(static fn($d) => preg_replace('/^\\s+|\\s+$/u', '', $d), explode(';', $row[S::residence_places]));

        if (count($dates) !== count($addresses)) {
            throw new Exception('Residence date count ' . count($dates) . ' does not match residence place count ' . count($addresses) . '.');
        }

        for ($i = 0; $i < count($dates); $i++) {
            $matches = [];
            $residence = new Residence();
            if (preg_match('/(.*?),\\s*(.*)/u', $addresses[$i], $matches)) {
                $residence->setAddress($matches[1]);
                $residence->setCity($this->findCity($matches[2]));
            } else {
                $residence->setCity($this->findCity($addresses[$i]));
            }
            $residence->setPerson($person);
            $residence->setDate($dates[$i]);
            $residence->setAddress($addresses[$i]);
            $this->em->persist($residence);
        }
    }

    /**
     * @required
     */
    public function setNotaryRepository(NotaryRepository $notaryRepository) : void {
        $this->notaryRepository = $notaryRepository;
    }

    /**
     * @required
     */
    public function setLedgerRepository(LedgerRepository $ledgerRepository) : void {
        $this->ledgerRepository = $ledgerRepository;
    }

    /**
     * @required
     */
    public function setRaceRepository(RaceRepository $raceRepository) : void {
        $this->raceRepository = $raceRepository;
    }

    /**
     * @required
     */
    public function setPersonRepository(PersonRepository $personRepository) : void {
        $this->personRepository = $personRepository;
    }

    /**
     * @required
     */
    public function setCityRepository(CityRepository $cityRepository) : void {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @required
     */
    public function setTransactionCategoryRepository(TransactionCategoryRepository $transactionCategoryRepository) : void {
        $this->transactionCategoryRepository = $transactionCategoryRepository;
    }

    /**
     * @required
     */
    public function setRelationshipCategoryRepository(RelationshipCategoryRepository $relationshipCategoryRepository) : void {
        $this->relationshipCategoryRepository = $relationshipCategoryRepository;
    }

    /**
     * @required
     */
    public function setLocationCategoryRepository(LocationCategoryRepository $locationCategoryRepository) : void {
        $this->locationCategoryRepository = $locationCategoryRepository;
    }

    /**
     * @required
     */
    public function setLocationRepository(LocationRepository $locationRepository) : void {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @required
     */
    public function setEventCategoryRepository(EventCategoryRepository $eventCategoryRepository) : void {
        $this->eventCategoryRepository = $eventCategoryRepository;
    }

    /**
     * @required
     */
    public function setBirthStatusRepository(BirthStatusRepository $birthStatusRepository) : void {
        $this->birthStatusRepository = $birthStatusRepository;
    }

    /**
     * @required
     */
    public function setWitnessCategoryRepository(WitnessCategoryRepository $witnessCategoryRepository) : void {
        $this->witnessCategoryRepository = $witnessCategoryRepository;
    }
}
