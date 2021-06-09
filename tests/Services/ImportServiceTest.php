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
use App\Entity\City;
use App\Entity\Ledger;
use App\Entity\Location;
use App\Entity\Notary;
use App\Entity\Person;
use App\Entity\Race;
use App\Entity\RelationshipCategory;
use App\Entity\Transaction;
use App\Entity\TransactionCategory;
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
        ];
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
     */
    public function createTransaction() : void {
        /** @var Ledger $ledger */
        $ledger = $this->getReference('ledger.1');
        /** @var Person $first */
        $first = $this->getReference('person.1');
        /** @var Person $second */
        $second = $this->getReference('person.2');
        $row = [
            N::first_party_spouse => null,
            N::first_party_notes => 'and wife',
            N::transaction_conjunction => 'from',
            N::second_party_spouse => null,
            N::second_party_notes => 'and husband',
            N::transaction_category => 'sale of property',
            N::transaction_date => '1790-04-20',
            N::ledger_page => 3,
            N::transaction_notes => 'Test transaction',
        ];

        $transaction = $this->importer->createTransaction($ledger, $first, $second, $row);
        $this->assertInstanceOf(Transaction::class, $transaction);
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
    public function findNewLocation() : void {
        $location = $this->importer->findLocation('Cheese and Crackers');
        $this->assertNotNull($location);
        $this->assertInstanceOf(Location::class, $location);
        $this->assertNull($location->getId());
        $this->assertSame('Cheese and Crackers', $location->getName());
    }

    /**
     * @test
     */
    public function addNullManumission() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $event = $this->importer->addManumission($person, []);
        $this->assertNull($event);
    }

    /**
     * @test
     */
    public function addManumission() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::manumission_date_written => '5 Jun 1771',
            S::manumission_date => '1771-06-05',
            S::manumission_place => 'Name 1',
        ];
        $event = $this->importer->addManumission($person, $row, 'Name 1');
        $this->assertNotNull($event);
        $this->assertSame('1771-06-05', $event->getDate());
        $this->assertSame('5 Jun 1771', $event->getWrittenDate());
    }

    /**
     * @test
     */
    public function addManumissionException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::manumission_date_written => '5 Jun 1771',
            S::manumission_date => '1771-06-05',
            S::manumission_place => 'Name 1',
        ];
        $this->importer->addManumission($person, $row, 'Not a name 1');
    }

    /**
     * @test
     */
    public function addBaptism() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::event_written_baptism_date => '5 Jun 1771',
            S::event_baptism_date => '1771-06-05',
            S::event_baptism_place => 'Name 1',
        ];
        $event = $this->importer->addBaptism($person, $row, 'Name 1');
        $this->assertNotNull($event);
        $this->assertSame('1771-06-05', $event->getDate());
        $this->assertSame('5 Jun 1771', $event->getWrittenDate());
    }

    /**
     * @test
     */
    public function addBaptismException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::event_written_baptism_date => '5 Jun 1771',
            S::event_baptism_date => '1771-06-05',
            S::event_baptism_place => 'Name 1',
            S::event_baptism_source => 'SLC something',
        ];
        $this->importer->addBaptism($person, $row, 'Not a name 1');
    }

    /**
     * @test
     */
    public function addAliases() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::alias => 'abc; def ; pdq;dre',
        ];
        $this->importer->addAliases($person, $row);
        $this->assertSame(['Alias 1', 'abc', 'def', 'pdq', 'dre'], $person->getAliases());
    }

    /**
     * @test
     */
    public function setNative() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::native => 'abc, def',
        ];
        $this->importer->setNative($person, $row);
        $this->assertSame('abc, def', $person->getNative());
    }

    /**
     * @test
     */
    public function addOccupations() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::occupation => '1900 fisherman; 1901 other thing; and another',
        ];
        $this->importer->addOccupations($person, $row);
        $this->assertCount(4, $person->getOccupations());
        $this->assertSame(['date' => 1601, 'occupation' => 'occupation 1'], $person->getOccupations()[0]);
        $this->assertSame(['date' => '1900', 'occupation' => 'fisherman'], $person->getOccupations()[1]);
        $this->assertSame(['date' => '1901', 'occupation' => 'other thing'], $person->getOccupations()[2]);
        $this->assertSame(['date' => null, 'occupation' => 'and another'], $person->getOccupations()[3]);
    }

    /**
     * @test
     */
    public function setWrittenRace() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::written_race => 'abc; def ; ghi',
        ];
        $this->importer->setWrittenRace($person, $row);
        $this->assertSame(['abc', 'def', 'ghi'], $person->getWrittenRaces());
    }

    /**
     * @test
     */
    public function setStatus() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::status => 'abc',
        ];
        $this->importer->setStatus($person, $row);
        $this->assertSame(['abc'], $person->getStatuses());
    }

    /**
     * @test
     */
    public function addBirth() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::birth_date => '1900-01',
            S::written_birth_date => 'abt Jan 1900',
            S::birth_place => 'Chatanooga',
        ];
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
     */
    public function addBirthException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::birth_date => '1900-01',
            S::written_birth_date => 'abt Jan 1900',
            S::birth_place => 'Chatanooga',
        ];
        $event = $this->importer->addBirth($person, $row, 'Not a category');
    }

    /**
     * @test
     */
    public function setBirthStatus() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::birth_status => 'Name 1',
        ];
        $this->importer->setBirthStatus($person, $row);
        $this->assertSame('Name 1', $person->getBirthStatus()->getName());
    }

    /**
     * @test
     */
    public function setBirthStatusException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $row = [
            S::birth_status => 'Label 45841',
        ];
        $this->importer->setBirthStatus($person, $row);
    }

    protected function setUp() : void {
        parent::setUp();
        $this->importer = $this->getContainer()->get(ImportService::class);
    }
}
