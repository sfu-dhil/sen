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
    private const SPLIT = '/\s*;\s*/u';

    private const TRIM = '/^\s*|\s*$/u';

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
            $ledger->setYear($year);
            $this->em->persist($ledger);
        }

        return $ledger;
    }

    /**
     * Find or create a race record by name.
     *
     * @throws Exception
     */
    public function findRace(?string $name) : ?Race {
        if ( ! $name) {
            return null;
        }
        $race = $this->raceRepository->findOneBy([
            'name' => $name,
        ]);
        if ( ! $race) {
            throw new Exception("Race ID {$name} not found.");
        }

        return $race;
    }

    /**
     * @throws Exception
     */
    public function otherSex(?string $sex) : string {
        if( ! $sex) {
            return '';
        }
        switch(mb_convert_case($sex[0], MB_CASE_UPPER)) {
            case Person::MALE:
                return Person::FEMALE;
            case Person::FEMALE:
                return Person::MALE;
            default:
                throw new Exception("Unknown sex {$sex}.");
        }
    }

    /**
     * Find or create a record for a person.
     *
     * @param mixed $sex
     *
     * @throws Exception
     */
    public function findPerson(?string $given, ?string $family, ?string $raceName = '', $sex = '') : Person {
        $first = mb_convert_case($given, MB_CASE_TITLE);
        $last = mb_convert_case($family, MB_CASE_TITLE);
        $person = $this->personRepository->findByName($first, $last);
        if( ! $person) {
            $person = new Person();
            $person->setFirstName($first);
            $person->setLastName($last);
            $this->em->persist($person);
        }
        $race = $this->raceRepository->findOneBy(['name' => $raceName]);
        if($race) {
            if($person->getRace() && $person->getRace() !== $race) {
                $this->logger->warning("Person {$person} has multiple races {$race} and {$person->getRace()}");
            }
            $person->setRace($race);
        }

        if($sex) {
            $s = mb_convert_case($sex[0], MB_CASE_UPPER);
            if($person->getSex() && $person->getSex() !== $s) {
                $this->logger->warning("Person {$person} has multiple sexes {$s} and {$person->getSex()}");
            }
            $person->setSex($s);
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
            $short = preg_replace('/[^a-z0-9]+/u', '-', mb_convert_case($label, MB_CASE_LOWER));
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
            $category->setLabel(mb_convert_case($name, MB_CASE_TITLE));
            $this->em->persist($category);
        }

        return $category;
    }

    /**
     * Create a transaction.
     *
     * @param mixed $categoryName
     *
     * @throws Exception
     */
    public function createTransaction(Ledger $ledger, Person $firstParty, Person $secondParty, array $row, $categoryName = 'spouse') : Transaction {
        $transaction = new Transaction();
        $transaction->setLedger($ledger);
        $transaction->setFirstParty($firstParty);
        $transaction->setFirstPartyNote($row[N::first_party_notes]);
        $transaction->setConjunction($row[N::transaction_conjunction]);
        $transaction->setSecondParty($secondParty);
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
        $string = preg_replace(self::TRIM, '', $string);
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
            $category->setLabel(mb_convert_case($name, MB_CASE_TITLE));
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

    public function createEvent(Person $person, array $row, EventCategory $category, $dateIdx, $writtenDateIdx, $placeIdx = null, $placeCategory = 'city') : Event {
        $event = new Event();
        $event->setCategory($category);
        $event->addParticipant($person);
        $event->setDate($this->parseDate($row[$dateIdx]));
        $event->setWrittenDate($row[$writtenDateIdx]);
        if (isset($row[$placeIdx]) && $row[$placeIdx]) {
            $event->setLocation($this->findLocation($row[$placeIdx], $placeCategory));
        }
        $this->em->persist($event);

        return $event;
    }

    public function addTitles(Person $person, array $row) : void {
        if( ! $row[S::title]) {
            return;
        }
        $titles = array_merge($person->getTitles(), preg_split(self::SPLIT, $row[S::title]));
        $person->setTitles($titles);
    }

    /**
     * @param mixed $row
     * @param mixed $name
     *
     * @throws Exception
     */
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

        return $this->createEvent($person, $row, $category, S::manumission_date, S::manumission_date_written, S::manumission_place);
    }

    /**
     * @param mixed $row
     * @param mixed $name
     *
     * @throws Exception
     */
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
        $event = $this->createEvent($person, $row, $category, S::event_baptism_date, S::event_written_baptism_date, S::event_baptism_place, 'church');
        if (isset($row[S::event_baptism_source])) {
            $event->setRecordSource($row[S::event_baptism_source]);
        }
        $this->em->persist($event);

        return $event;
    }

    public function addAliases(Person $person, $row) : void {
        if ( ! isset($row[S::alias]) || ! $row[S::alias]) {
            return;
        }
        $aliases = preg_split(self::SPLIT, $row[S::alias]);
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
        $list = preg_split(self::SPLIT, $row[S::occupation]);
        foreach ($list as $data) {
            $m = [];
            if (preg_match('/^\s*(\d{4})\s*(.*)\s*$/u', $data, $m)) {
                $occupations[] = ['date' => $m[1], 'occupation' => $m[2]];
            } else {
                $occupations[] = ['date' => null, 'occupation' => preg_replace(self::TRIM, '', $data)];
            }
        }
        $person->setOccupations(array_merge($person->getOccupations(), $occupations));
    }

    public function setWrittenRace(Person $person, array $row) : void {
        if ( ! isset($row[S::written_race]) || ! $row[S::written_race]) {
            return;
        }

        $races = preg_split(self::SPLIT, $row[S::written_race]);
        $person->setWrittenRaces(array_merge($person->getWrittenRaces(), $races));
    }

    public function setStatus(Person $person, array $row) : void {
        if ( ! isset($row[S::status]) || ! $row[S::status]) {
            return;
        }
        $person->setStatuses(array_merge($person->getStatuses(), [$row[S::status]]));
    }

    /**
     * @param mixed $name
     *
     * @throws Exception
     */
    public function addBirth(Person $person, array $row, $name = 'birth') : ?Event {
        if ( ! isset($row[S::birth_date]) || ! $row[S::birth_date]) {
            return null;
        }

        $category = $this->eventCategoryRepository->findOneBy(['name' => $name]);
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

    /**
     * @throws Exception
     */
    public function setBirthStatus(Person $person, array $row) : void {
        if ( ! isset($row[S::birth_status]) || ! $row[S::birth_status]) {
            return;
        }
        $status = $this->birthStatusRepository->findOneBy(['name' => $row[S::birth_status]]);
        if ( ! $status) {
            throw new Exception('Birth status is missing for ' . $row[S::birth_status]);
        }
        $person->setBirthStatus($status);
    }

    /**
     * @throws Exception
     */
    public function createRelationship(Person $person, Person $related, string $categoryName) : Relationship {
        $category = $this->relationshipCategoryRepository->findOneBy(['name' => $categoryName]);
        if ( ! $category) {
            throw new Exception("Relationship category {$categoryName} is missing.");
        }
        $relationship = new Relationship();
        $relationship->setPerson($person);
        $relationship->setRelation($related);
        $relationship->setCategory($category);
        $this->em->persist($relationship);

        return $relationship;
    }

    /**
     * @throws Exception
     */
    public function addRelationship(Person $person, array $row, Person $relation, string $relationshipName, string $relationName) : array {
        $relationship = $this->createRelationship($person, $relation, $relationshipName);
        $inverse = $this->createRelationship($relation, $person, $relationName);

        return [$relationship, $inverse];
    }

    /**
     * @throws Exception
     */
    public function addParents(Person $person, array $row, string $fatherCategory = 'father', string $motherCategory = 'mother', string $childCategory = 'child') : array {
        $relationships = [];
        if ($row[S::father_first_name] || $row[S::father_last_name]) {
            $father = $this->findPerson($row[S::father_first_name], $row[S::father_last_name], null, 'Male');
            $added = $this->addRelationship($person, $row, $father, $fatherCategory, $childCategory);
            $relationships = array_merge($relationships, $added);
        }

        if ($row[S::mother_first_name] || $row[S::mother_last_name]) {
            $mother = $this->findPerson($row[S::mother_first_name], $row[S::mother_last_name], null, 'Female');
            $added = $this->addRelationship($person, $row, $mother, $motherCategory, $childCategory);
            $relationships = array_merge($relationships, $added);
        }

        return $relationships;
    }

    /**
     * @throws Exception
     */
    public function addGodParents(Person $person, array $row, string $godfatherCategory = 'godfather', string $godmotherCategory = 'godmother', string $godchildCategory = 'godchild') : array {
        $relationships = [];
        if ($row[S::godfather_first_name] || $row[S::godfather_last_name]) {
            $godfather = $this->findPerson($row[S::godfather_first_name], $row[S::godfather_last_name], null, 'Male');
            $added = $this->addRelationship($person, $row, $godfather, $godfatherCategory, $godchildCategory);
            $relationships = array_merge($relationships, $added);
        }

        if ($row[S::godmother_first_name] || $row[S::godmother_last_name]) {
            $godmother = $this->findPerson($row[S::godmother_first_name], $row[S::godmother_last_name], null, 'Male');
            $added = $this->addRelationship($person, $row, $godmother, $godmotherCategory, $godchildCategory);
            $relationships = array_merge($relationships, $added);
        }

        return $relationships;
    }

    /**
     * @throws Exception
     *
     * @return Witness[]
     */
    public function addEventWitnesses(Event $event, string $categoryName, Person ...$people) : array {
        $category = $this->witnessCategoryRepository->findOneBy(['name' => $categoryName]);
        if ( ! $category) {
            throw new Exception("Event category {$categoryName} is missing.");
        }
        $witnesses = [];
        foreach ($people as $person) {
            $witness = new Witness();
            $witness->setPerson($person);
            $witness->setEvent($event);
            $witness->setCategory($category);
            $this->em->persist($witness);
            $witnesses[] = $witness;
        }

        return $witnesses;
    }

    /**
     * @throws Exception
     */
    public function addMarriage(Person $person, array $row, string $categoryName = 'wedding') : ?Event {
        if ( ! isset($row[S::event_written_marriage_date]) || ! $row[S::event_written_marriage_date]) {
            return null;
        }
        if ( ! $row[S::spouse_first_name] && ! $row[S::spouse_last_name]) {
            throw new Exception('Written marriage date without spouse name');
        }
        $category = $this->eventCategoryRepository->findOneBy(['name' => $categoryName]);
        if ( ! $category) {
            throw new Exception("Marriage event category {$categoryName} is missing.");
        }
        $event = $this->createEvent($person, $row, $category, S::event_marriage_date, S::event_written_marriage_date, S::event_marriage_place, 'church');
        $event->setNote($row[S::event_marriage_memo]);
        $event->setRecordSource($row[S::event_marriage_source]);
        $this->em->persist($event);

        return $event;
    }

    /**
     * @throws Exception
     *
     * @return Relationship[]
     */
    public function addSpouse(Person $person, array $row, Person $spouse, string $categoryName = 'spouse') : array {
        return $this->addRelationship($person, $row, $spouse, $categoryName, $categoryName);
    }

    /**
     * @param mixed $categoryName
     *
     * @throws Exception
     */
    public function addMarriageWitnesses(Event $marriage, array $row, $categoryName = 'witness') : array {
        $people = [];
        if ((isset($row[S::event_marriage_witness1_first_name]) && $row[S::event_marriage_witness1_first_name])
            || isset($row[S::event_marriage_witness1_last_name]) && $row[S::event_marriage_witness1_last_name]) {
            $people[] = $this->findPerson($row[S::event_marriage_witness1_first_name], $row[S::event_marriage_witness1_last_name]);
        }
        if ((isset($row[S::event_marriage_witness2_first_name]) && $row[S::event_marriage_witness2_first_name])
            || isset($row[S::event_marriage_witness2_last_name]) && $row[S::event_marriage_witness2_last_name]) {
            $people[] = $this->findPerson($row[S::event_marriage_witness2_first_name], $row[S::event_marriage_witness2_last_name]);
        }

        return $this->addEventWitnesses($marriage, $categoryName, ...$people);
    }

    /**
     * @param mixed $categoryName
     *
     * @throws Exception
     */
    public function addDeath(Person $person, array $row, $categoryName = 'death') : ?Event {
        if ( ! isset($row[S::event_death_date]) || ! $row[S::event_death_date]) {
            return null;
        }

        $category = $this->eventCategoryRepository->findOneBy(['name' => $categoryName]);
        if ( ! $category) {
            throw new Exception("event category {$categoryName} is missing.");
        }

        return $this->createEvent($person, $row, $category, S::event_death_date, S::event_written_death_date, S::event_death_place);
    }

    /**
     * @throws Exception
     *
     * @return Residence[]
     */
    public function addResidences(Person $person, array $row) : array {
        $residences = [];
        if ( ! $row[S::residence_dates] || ! $row[S::residence_places]) {
            return $residences;
        }
        $dates = preg_split(self::SPLIT, $row[S::residence_dates]);
        $addresses = preg_split(self::SPLIT, $row[S::residence_places]);

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
            $this->em->persist($residence);
            $residences[] = $residence;
        }

        return $residences;
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
