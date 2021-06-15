<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Services;

use App\DataFixtures\CityFixtures;
use App\DataFixtures\EventCategoryFixtures;
use App\DataFixtures\EventFixtures;
use App\DataFixtures\LedgerFixtures;
use App\DataFixtures\LocationCategoryFixtures;
use App\DataFixtures\LocationFixtures;
use App\DataFixtures\NotaryFixtures;
use App\DataFixtures\PersonFixtures;
use App\DataFixtures\RaceFixtures;
use App\DataFixtures\RelationshipCategoryFixtures;
use App\DataFixtures\TransactionCategoryFixtures;
use App\DataFixtures\WitnessCategoryFixtures;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Ledger;
use App\Entity\Location;
use App\Entity\Notary;
use App\Entity\Person;
use App\Entity\Race;
use App\Entity\Relationship;
use App\Entity\RelationshipCategory;
use App\Entity\Transaction;
use App\Entity\TransactionCategory;
use App\Entity\Witness;
use App\Services\ImportService;
use App\Util\NotaryColumnDefinitions as N;
use App\Util\SacramentColumnDefinitions as S;
use Exception;
use Nines\UtilBundle\Tests\ServiceBaseCase;
use App\Entity\LocationCategory;

class ImportServiceTest extends ServiceBaseCase {
    private ImportService $importer;

    protected function fixtures() : array {
        return [
            NotaryFixtures::class,
            LedgerFixtures::class,
            RaceFixtures::class,
            PersonFixtures::class,
            CityFixtures::class,
            RelationshipCategoryFixtures::class,
            TransactionCategoryFixtures::class,
            LocationCategoryFixtures::class,
            LocationFixtures::class,
            EventCategoryFixtures::class,
            EventFixtures::class,
            WitnessCategoryFixtures::class,
        ];
    }

    protected function getRow($data = []) : array {
        $row = array_fill(0, S::notes + 1, '');
        foreach ($data as $k => $v) {
            $row[$k] = $v;
        }

        return $row;
    }

    /**
     * @test
     */
    public function config() : void {
        $this->assertInstanceOf(ImportService::class, $this->importer);
    }

    /**
     * @test
     */
    public function findNotary() : void {
        $notary = $this->importer->findNotary('Name 1');
        $this->assertNotNull($notary);
        $this->assertInstanceOf(Notary::class, $notary);
        $this->assertNotNull($notary->getId());
    }

    /**
     * @test
     */
    public function findNewNotary() : void {
        $notary = $this->importer->findNotary('Cheese and Crackers');
        $this->assertNotNull($notary);
        $this->assertInstanceOf(Notary::class, $notary);
        $this->assertNull($notary->getId());
    }

    /**
     * @test
     */
    public function findLedger() : void {
        $notary = $this->getReference('notary.1');
        $ledger = $this->importer->findLedger($notary, 'Volume 1', 1234);
        $this->assertInstanceOf(Ledger::class, $ledger);
        $this->assertNotNull($ledger->getId());
        $this->assertSame(1801, $ledger->getYear());
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @group d
     *
     * @test
     */
    public function findNewLedger() : void {
        $notary = $this->getReference('notary.1');
        $ledger = $this->importer->findLedger($notary, 'Volume 77', 1234);
        $this->assertInstanceOf(Ledger::class, $ledger);
        $this->assertNull($ledger->getId());
        $this->assertSame(1234, $ledger->getYear());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function findRace() : void {
        $race = $this->importer->findRace('Name 1');
        $this->assertNotNull($race);
        $this->assertInstanceOf(Race::class, $race);
        $this->assertNotNull($race->getId());
    }

    /**
     * @test
     */
    public function findNewRace() : void {
        $this->expectException(Exception::class);
        $race = $this->importer->findRace('Cheese and Crackers');
    }

    /**
     * @dataProvider findPersonData
     *
     * @param mixed $given
     * @param mixed $family
     * @param mixed $race
     * @param mixed $sex
     * @param mixed $existing
     * @param mixed $tests
     *
     * @test
     *
     * @throws Exception
     */
    public function findPerson($given, $family, $race, $sex, $existing, $tests = []) : void {
        $person = $this->importer->findPerson($given, $family, $race, $sex);
        $this->assertNotNull($person);
        if ($existing) {
            $this->assertNotNull($person->getId());
        } else {
            $this->assertNull($person->getId());
        }
        foreach ($tests as $method => $expected) {
            $this->assertSame($expected, $person->{$method}());
        }
    }

    public function findPersonData() : array {
        return [
            ['Firstname 1', 'Lastname 1', null, null, true, [
                'getFirstName' => 'FirstName 1',
                'getLastName' => 'LastName 1',
            ]],
            ['FIRSTNAME 1', 'LASTNAME 1', null, 'Female', true, [
                'getFirstName' => 'FirstName 1',
                'getLastName' => 'LastName 1',
            ]],
            ['firstname 1', 'lastname 1', null, null, true, [
                'getFirstName' => 'FirstName 1',
                'getLastName' => 'LastName 1',
            ]],
            ['New firstname', 'New lastname', null, null, false, [
                'getFirstName' => 'New Firstname',
                'getLastName' => 'New Lastname',
            ]],
            ['New firstname', 'New lastname', null, null, false, [
                'getFirstName' => 'New Firstname',
                'getLastName' => 'New Lastname',
            ]],
            ['NEW FIRSTNAME', 'NEW LASTNAME', null, null, false, [
                'getFirstName' => 'New Firstname',
                'getLastName' => 'New Lastname',
            ]],
            ['new firstname', 'new lastname', null, null, false, [
                'getFirstName' => 'New Firstname',
                'getLastName' => 'New Lastname',
            ]],

            // tests for gender
            ['new firstname', 'new lastname', null, 'Male', false, [
                'getSex' => 'M',
            ]],
            ['new firstname', 'new lastname', null, 'M', false, [
                'getSex' => 'M',
            ]],
            ['new firstname', 'new lastname', null, 'male', false, [
                'getSex' => 'M',
            ]],
            ['new firstname', 'new lastname', null, 'Rale', false, [
                'getSex' => null,
            ]],

            ['new firstname', 'new lastname', null, 'Female', false, [
                'getSex' => 'F',
            ]],
            ['new firstname', 'new lastname', null, 'Fem', false, [
                'getSex' => 'F',
            ]],
            ['new firstname', 'new lastname', null, 'female', false, [
                'getSex' => 'F',
            ]],
            ['new firstname', 'new lastname', null, 're', false, [
                'getSex' => null,
            ]],
        ];
    }

    /**
     * @test
     */
    public function findCity() : void {
        $city = $this->importer->findCity('Name 1');
        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getId());
    }

    /**
     * @test
     */
    public function findNewCity() : void {
        $city = $this->importer->findCity('Cheese and Crackers');
        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNull($city->getId());
    }

    /**
     * @test
     */
    public function findTransactionCategory() : void {
        $transactionCategory = $this->importer->findTransactionCategory('Label 1');
        $this->assertNotNull($transactionCategory);
        $this->assertInstanceOf(TransactionCategory::class, $transactionCategory);
        $this->assertNotNull($transactionCategory->getId());
    }

    /**
     * @test
     */
    public function findNewTransactionCategory() : void {
        $transactionCategory = $this->importer->findTransactionCategory('Cheese and Crackers');
        $this->assertNotNull($transactionCategory);
        $this->assertInstanceOf(TransactionCategory::class, $transactionCategory);
        $this->assertNull($transactionCategory->getId());
        $this->assertSame('Cheese and Crackers', $transactionCategory->getLabel());
        $this->assertSame('cheese-and-crackers', $transactionCategory->getName());
    }

    /**
     * @test
     */
    public function findRelationshipCategory() : void {
        $relationshipCategory = $this->importer->findRelationshipCategory('Name 1');
        $this->assertNotNull($relationshipCategory);
        $this->assertInstanceOf(RelationshipCategory::class, $relationshipCategory);
        $this->assertNotNull($relationshipCategory->getId());
    }

    /**
     * @test
     */
    public function findNewRelationshipCategory() : void {
        $relationshipCategory = $this->importer->findRelationshipCategory('Cheese and Crackers');
        $this->assertNotNull($relationshipCategory);
        $this->assertInstanceOf(RelationshipCategory::class, $relationshipCategory);
        $this->assertNull($relationshipCategory->getId());
        $this->assertSame('Cheese And Crackers', $relationshipCategory->getLabel());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function createTransaction() : void {
        /** @var Ledger $ledger */
        $ledger = $this->getReference('ledger.1');
        /** @var Person $first */
        $first = $this->getReference('person.1');
        /** @var Person $second */
        $second = $this->getReference('person.2');
        $row = $this->getRow([
            N::first_party_spouse => null,
            N::first_party_notes => 'and wife',
            N::transaction_conjunction => 'from',
            N::second_party_spouse => null,
            N::second_party_notes => 'and husband',
            N::transaction_category => 'sale of property',
            N::transaction_date => '1790-04-20',
            N::ledger_page => 3,
            N::transaction_notes => 'Test transaction',
        ]);

        $transaction = $this->importer->createTransaction($ledger, $first, $second, $row);
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertSame('and wife', $transaction->getFirstPartyNote());
        $this->assertSame('from', $transaction->getConjunction());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function createTransactionSpouses() : void {
        /** @var Ledger $ledger */
        $ledger = $this->getReference('ledger.1');
        /** @var Person $first */
        $first = $this->getReference('person.1');
        /** @var Person $second */
        $second = $this->getReference('person.2');
        $row = $this->getRow([
            N::first_party_spouse => 'Mary J',
            N::transaction_conjunction => 'from',
            N::second_party_spouse => 'Tommy M',
            N::transaction_category => 'sale of property',
            N::transaction_date => '1790-04-20',
            N::ledger_page => 3,
            N::transaction_notes => 'Test transaction',
        ]);

        $transaction = $this->importer->createTransaction($ledger, $first, $second, $row);
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertSame('from', $transaction->getConjunction());
    }

    /**
     * @dataProvider parseDateData
     *
     * @test
     */
    public function parseDate(?string $date, ?string $expected) : void {
        $this->assertSame($expected, $this->importer->parseDate($date));
    }

    public function parseDateData() : array {
        return [
            [null, null],
            ['cheese', null],
            ['', null],
            ['1900', '1900'],
            ['1900-01', '1900-01'],
            ['1900-01-01', '1900-01-01'],
            ['   1900', '1900'],
            ['  1900-01', '1900-01'],
            [' 1900-01-01', '1900-01-01'],
            ['1900   ', '1900'],
            ['1900-01  ', '1900-01'],
            ['1900-01-01 ', '1900-01-01'],
        ];
    }

    /**
     * @test
     */
    public function findLocationCategory() : void {
        $locationCategory = $this->importer->findLocationCategory('Name 1');
        $this->assertNotNull($locationCategory);
        $this->assertInstanceOf(LocationCategory::class, $locationCategory);
        $this->assertNotNull($locationCategory->getId());
    }

    /**
     * @test
     */
    public function findNewLocationCategory() : void {
        $locationCategory = $this->importer->findLocationCategory('Cheese and Crackers');
        $this->assertNotNull($locationCategory);
        $this->assertInstanceOf(LocationCategory::class, $locationCategory);
        $this->assertNull($locationCategory->getId());
        $this->assertSame('Cheese And Crackers', $locationCategory->getLabel());
    }

    /**
     * @test
     */
    public function findLocation() : void {
        $location = $this->importer->findLocation('Name 1', 'Name 1');
        $this->assertNotNull($location);
        $this->assertInstanceOf(Location::class, $location);
        $this->assertNotNull($location->getId());
    }

    /**
     * @test
     */
    public function findNullLocation() : void {
        $location = $this->importer->findLocation('', 'Name 1');
        $this->assertNull($location);
    }

    /**
     * @test
     */
    public function findNewLocation() : void {
        $location = $this->importer->findLocation('Cheese and Crackers');
        $this->assertNotNull($location);
        $this->assertInstanceOf(Location::class, $location);
        $this->assertNull($location->getId());
        $this->assertSame('Cheese and Crackers', $location->getName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addManumission() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::manumission_date_written => '5 Jun 1771',
            S::manumission_date => '1771-06-05',
            S::manumission_place => 'Name 1',
        ]);
        $event = $this->importer->addManumission($person, $row, 'Name 1');
        $this->assertNotNull($event);
        $this->assertSame('1771-06-05', $event->getDate());
        $this->assertSame('5 Jun 1771', $event->getWrittenDate());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addNullManumission() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::manumission_place => '',
        ]);
        $event = $this->importer->addManumission($person, $row, 'Name 1');
        $this->assertNull($event);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addManumissionException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::manumission_date_written => '5 Jun 1771',
            S::manumission_date => '1771-06-05',
            S::manumission_place => 'Name 1',
        ]);
        $this->importer->addManumission($person, $row, 'Not a name 1');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addBaptism() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::event_written_baptism_date => '5 Jun 1771',
            S::event_baptism_date => '1771-06-05',
            S::event_baptism_place => 'Name 1',
        ]);
        $event = $this->importer->addBaptism($person, $row, 'Name 1');
        $this->assertNotNull($event);
        $this->assertSame('1771-06-05', $event->getDate());
        $this->assertSame('5 Jun 1771', $event->getWrittenDate());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addNullBaptism() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
        ]);
        $event = $this->importer->addBaptism($person, $row, 'Name 1');
        $this->assertNull($event);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addBaptismException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::event_written_baptism_date => '5 Jun 1771',
            S::event_baptism_date => '1771-06-05',
            S::event_baptism_place => 'Name 1',
            S::event_baptism_source => 'SLC something',
        ]);
        $this->importer->addBaptism($person, $row, 'Not a name 1');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addAliases() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::alias => 'abc; def ; pdq;dre',
        ]);
        $this->importer->addAliases($person, $row);
        $this->assertSame(['Alias 1', 'abc', 'def', 'pdq', 'dre'], $person->getAliases());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addNullAliases() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::alias => '',
        ]);
        $this->importer->addAliases($person, $row);
        $this->assertSame(['Alias 1'], $person->getAliases());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setNative() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::native => 'abc, def',
        ]);
        $this->importer->setNative($person, $row);
        $this->assertSame('abc, def', $person->getNative());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setNullNative() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::native => '',
        ]);
        $this->importer->setNative($person, $row);
        $this->assertSame('Native 1', $person->getNative());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addOccupations() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::occupation => '1900 fisherman; 1901 other thing; and another',
        ]);
        $this->importer->addOccupations($person, $row);
        $this->assertCount(4, $person->getOccupations());
        $this->assertSame(['date' => 1601, 'occupation' => 'occupation 1'], $person->getOccupations()[0]);
        $this->assertSame(['date' => '1900', 'occupation' => 'fisherman'], $person->getOccupations()[1]);
        $this->assertSame(['date' => '1901', 'occupation' => 'other thing'], $person->getOccupations()[2]);
        $this->assertSame(['date' => null, 'occupation' => 'and another'], $person->getOccupations()[3]);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addNullOccupations() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::occupation => '',
        ]);
        $this->importer->addOccupations($person, $row);
        $this->assertCount(1, $person->getOccupations());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setWrittenRace() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::written_race => 'abc; def ; ghi',
        ]);
        $this->importer->setWrittenRace($person, $row);
        $this->assertSame(['abc', 'def', 'ghi'], $person->getWrittenRaces());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setNullWrittenRace() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::written_race => '',
        ]);
        $this->importer->setWrittenRace($person, $row);
        $this->assertSame([], $person->getWrittenRaces());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setStatus() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::status => 'abc',
        ]);
        $this->importer->setStatus($person, $row);
        $this->assertSame(['abc'], $person->getStatuses());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setNullStatus() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::status => '',
        ]);
        $this->importer->setStatus($person, $row);
        $this->assertSame([], $person->getStatuses());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addBirth() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::birth_date => '1900-01',
            S::written_birth_date => 'abt Jan 1900',
            S::birth_place => 'Chatanooga',
        ]);
        $event = $this->importer->addBirth($person, $row, 'Name 1');
        $this->assertSame($person, $event->getParticipants()[0]);
        $this->assertSame('1900-01', $event->getDate());
        $this->assertSame('abt Jan 1900', $event->getWrittenDate());
        $location = $event->getLocation();
        $this->assertNotNull($location);
        $this->assertSame('Chatanooga', $location->getName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addNullBirth() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::birth_date => '',
        ]);
        $event = $this->importer->addBirth($person, $row, 'Name 1');
        $this->assertNull($event);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addBirthException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::birth_date => '1900-01',
            S::written_birth_date => 'abt Jan 1900',
            S::birth_place => 'Chatanooga',
        ]);
        $event = $this->importer->addBirth($person, $row, 'Not a category');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setBirthStatus() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::birth_status => 'Name 1',
        ]);
        $this->importer->setBirthStatus($person, $row);
        $this->assertSame('Name 1', $person->getBirthStatus()->getName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setNullBirthStatus() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::birth_status => '',
        ]);
        $this->importer->setBirthStatus($person, $row);
        $this->assertSame('Name 1', $person->getBirthStatus()->getName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function setBirthStatusException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::birth_status => 'Label 45841',
        ]);
        $this->importer->setBirthStatus($person, $row);
    }

    /**
     * @test
     */
    public function addNullRelationship() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow();
        $result = $this->importer->addRelationship($person, $row, 1, 1, 'M', 'foo', 'bar');
        $this->assertCount(0, $result);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function createRelationship() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.2');

        $relationship = $this->importer->createRelationship($person, $relation, 'Name 1');
        $this->assertNotNull($relationship);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function createRelationshipException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.2');

        $relationship = $this->importer->createRelationship($person, $relation, 'Phony baloney');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addRelationship() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.2');

        $row = $this->getRow([
            S::father_first_name => $relation->getFirstName(),
            S::father_last_name => $relation->getLastName(),
        ]);

        list($r, $i) = $this->importer->addRelationship($person, $row, S::father_first_name, S::father_last_name, 'Male', 'Name 1', 'Name 2');
        $this->assertNotNull($r);
        $this->assertInstanceOf(Relationship::class, $r);
        $this->assertSame($person, $r->getPerson());
        $this->assertSame($relation, $r->getRelation());
        $this->assertSame('Name 1', $r->getCategory()->getName());

        $this->assertNotNull($i);
        $this->assertInstanceOf(Relationship::class, $i);
        $this->assertSame($relation, $i->getPerson());
        $this->assertSame($person, $i->getRelation());
        $this->assertSame('Name 2', $i->getCategory()->getName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addParents() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');

        $row = $this->getRow([
            S::father_first_name => 'Joe',
            S::father_last_name => 'Quimby',
            S::mother_first_name => 'Clara',
            S::mother_last_name => 'Quimby',
        ]);

        $added = $this->importer->addParents($person, $row, 'Name 1', 'Name 2', 'Name 3');
        $this->assertCount(4, $added);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addGodParents() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');

        $row = $this->getRow([
            S::godfather_first_name => 'Joe',
            S::godfather_last_name => 'Quimby',
            S::godmother_first_name => 'Clara',
            S::godmother_last_name => 'Quimby',
        ]);

        $added = $this->importer->addGodParents($person, $row, 'Name 1', 'Name 2', 'Name 3');
        $this->assertCount(4, $added);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addEventWitnesses() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Event $event */
        $event = $this->getReference('event.1');
        $witnesses = $this->importer->addEventWitnesses($event, 'Name 1', $person);
        $this->assertCount(1, $witnesses);
        $this->assertSame($person, $witnesses[0]->getPerson());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addEventWitnessesException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Event $event */
        $event = $this->getReference('event.1');
        $witnesses = $this->importer->addEventWitnesses($event, 'Cheese Party Witness', $person);
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function addMarriage() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::spouse_first_name => 'Jane',
            S::spouse_last_name => 'Quimby',
            S::event_marriage_place => 'St Swithens',
            S::event_marriage_date => '1900-01-01',
            S::event_written_marriage_date => 'abt 1900',
            S::event_marriage_memo => 'In a records',
        ]);

        $event = $this->importer->addMarriage($person, $row, 'Name 1');
        $this->assertNotNull($event);
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function addNullMarriage() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::spouse_first_name => 'Jane',
            S::spouse_last_name => 'Quimby',
            S::event_marriage_place => 'St Swithens',
            S::event_marriage_date => '1900-01-01',
            S::event_written_marriage_date => '',
            S::event_marriage_memo => 'In a records',
        ]);

        $event = $this->importer->addMarriage($person, $row, 'Name 1');
        $this->assertNull($event);
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function addMarriageMissingSpouseException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::event_marriage_place => 'St Swithens',
            S::event_marriage_date => '1900-01-01',
            S::event_written_marriage_date => 'abt 1900',
            S::event_marriage_memo => 'In a records',
        ]);
        $event = $this->importer->addMarriage($person, $row, 'Name 1');
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function addMarriageMissingCategoryException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::spouse_first_name => 'Jane',
            S::spouse_last_name => 'Quimby',
            S::event_marriage_place => 'St Swithens',
            S::event_marriage_date => '1900-01-01',
            S::event_written_marriage_date => 'abt 1900',
            S::event_marriage_memo => 'In a records',
        ]);
        $event = $this->importer->addMarriage($person, $row, 'Name 1235');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addMaleSpouse() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');

        $row = $this->getRow([
            S::spouse_first_name => 'Jane',
            S::spouse_last_name => 'Quimby',
        ]);

        list($r, $i) = $this->importer->addSpouse($person, $row, 'Name 1');
        $this->assertNotNull($r);
        $this->assertSame($person, $r->getPerson());
        $this->assertNotNull($i);
        $this->assertNotNull($i->getPerson());
        $this->assertSame('Jane', $i->getPerson()->getFirstName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addFemaleSpouse() : void {
        /** @var Person $person */
        $person = $this->getReference('person.2');

        $row = $this->getRow([
            S::spouse_first_name => 'Joe',
            S::spouse_last_name => 'Quimby',
        ]);

        list($r, $i) = $this->importer->addSpouse($person, $row, 'Name 1');
        $this->assertNotNull($r);
        $this->assertSame($person, $r->getPerson());
        $this->assertNotNull($i);
        $this->assertNotNull($i->getPerson());
        $this->assertSame('Joe', $i->getPerson()->getFirstName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addNullSpouse() : void {
        /** @var Person $person */
        $person = $this->getReference('person.2');

        $row = $this->getRow([
            S::spouse_first_name => '',
            S::spouse_last_name => '',
        ]);

        $result = $this->importer->addSpouse($person, $row, 'Name 1');
        $this->assertCount(0, $result);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addMarriageWitnesses() : void {
        /** @var Event $event */
        $event = $this->getReference('event.1');
        $row = $this->getRow([
            S::event_marriage_witness1_first_name => 'Homer',
            S::event_marriage_witness1_last_name => 'Simpson',
            S::event_marriage_witness2_first_name => 'Marg',
            S::event_marriage_witness2_last_name => 'Simpson',
        ]);
        $witnesses = $this->importer->addMarriageWitnesses($event, $row, 'Name 1');
        $this->assertCount(2, $witnesses);
        $this->assertInstanceOf(Witness::class, $witnesses[0]);
        $this->assertSame($event, $witnesses[0]->getEvent());
        $this->assertSame('Homer', $witnesses[0]->getPerson()->getFirstName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addDeath() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::event_death_date => '1900-01',
            S::event_written_death_date => 'abt Jan 1900',
            S::event_death_place => 'Chatanooga',
        ]);
        $event = $this->importer->addDeath($person, $row, 'Name 1');
        $this->assertSame($person, $event->getParticipants()[0]);
        $this->assertSame('1900-01', $event->getDate());
        $this->assertSame('abt Jan 1900', $event->getWrittenDate());
        $location = $event->getLocation();
        $this->assertNotNull($location);
        $this->assertSame('Chatanooga', $location->getName());
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addNullDeath() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::event_death_date => '',
        ]);
        $event = $this->importer->addDeath($person, $row, 'Name 1');
        $this->assertNull($event);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function addDeathException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::event_death_date => '1900-01',
            S::event_written_death_date => 'abt Jan 1900',
            S::event_death_place => 'Chatanooga',
        ]);
        $event = $this->importer->addDeath($person, $row, 'Not a category');
    }

    public function testAddResidences() {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::residence_dates => '1934; 1922',
            S::residence_places => '123 Some St, Nola; Chatanooga'
        ]);
        $residences = $this->importer->addResidences($person, $row);
        $this->assertCount(2, $residences);
        $this->assertEquals($person, $residences[0]->getPerson());
        $this->assertEquals("1934", $residences[0]->getDate());
        $this->assertEquals('123 Some St', $residences[0]->getAddress());
        $this->assertEquals('Nola', $residences[0]->getCity()->getName());

        $this->assertEquals($person, $residences[1]->getPerson());
        $this->assertEquals("1922", $residences[1]->getDate());
        $this->assertEquals(null, $residences[1]->getAddress());
        $this->assertEquals('Chatanooga', $residences[1]->getCity()->getName());
    }

    public function testAddNullResidences() {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::residence_dates => '',
            S::residence_places => ''
        ]);
        $residences = $this->importer->addResidences($person, $row);
        $this->assertCount(0, $residences);
    }

    public function testAddResidencesException() {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = $this->getRow([
            S::residence_dates => '1900',
            S::residence_places => '123 Some St, Nola; Chatanooga'
        ]);
        $residences = $this->importer->addResidences($person, $row);
        $this->assertCount(0, $residences);
    }

    protected function setUp() : void {
        parent::setUp();
        $this->importer = $this->getContainer()->get(ImportService::class);
    }
}
