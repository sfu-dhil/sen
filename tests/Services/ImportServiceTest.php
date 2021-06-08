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
use App\Entity\LocationCategory;
use App\Entity\Notary;
use App\Entity\Person;
use App\Entity\Race;
use App\Entity\RelationshipCategory;
use App\Entity\Transaction;
use App\Entity\TransactionCategory;
use App\Services\ImportService;
use App\Util\NotaryColumnDefinitions as N;
use Nines\UtilBundle\Tests\ServiceBaseCase;

/**
 * @internal
 * @coversNothing
 */
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

    public function testConfig() : void {
        static::assertInstanceOf(ImportService::class, $this->importer);
    }

    public function testFindNotary() : void {
        $notary = $this->importer->findNotary('Name 1');
        static::assertInstanceOf(Notary::class, $notary);
        static::assertNotNull($notary->getId());
        static::assertSame('Name 1', $notary->getName());
    }

    public function testNewFindNotary() : void {
        $notary = $this->importer->findNotary('Name 5');
        static::assertInstanceOf(Notary::class, $notary);

        static::assertSame('Name 5', $notary->getName());
    }

    public function testFindLedger() : void {
        $notary = $this->getReference('notary.1');
        $ledger = $this->importer->findLedger($notary, 'Volume 1', 1801);
        static::assertInstanceOf(Ledger::class, $ledger);
        static::assertNotNull($ledger->getId());
        static::assertSame(1801, $ledger->getYear());
    }

    public function testNewFindLedger() : void {
        $notary = $this->getReference('notary.1');
        $ledger = $this->importer->findLedger($notary, 'Volume 3', 1801);
        static::assertInstanceOf(Ledger::class, $ledger);

        static::assertSame(1801, $ledger->getYear());
    }

    public function testFindRace() : void {
        $race = $this->importer->findRace('Name 1');
        static::assertInstanceOf(Race::class, $race);
        static::assertNotNull($race->getId());
        static::assertSame('Name 1', $race->getName());
    }

    public function testNewFindRace() : void {
        $race = $this->importer->findRace('Name 6');
        static::assertInstanceOf(Race::class, $race);

        static::assertSame('Name 6', $race->getName());
    }

    /**
     * @dataProvider findPersonData
     *
     * @param mixed $given
     * @param mixed $family
     * @param mixed $raceName
     */
    public function testFindPerson($given, $family, $raceName) : void {
        $found = $this->importer->findPerson($given, $family, $raceName);
        static::assertInstanceOf(Person::class, $found);
        static::assertNotNull($found->getId());
        static::assertSame(\mb_convert_case($family, \MB_CASE_TITLE), $found->getLastName());
    }

    public function findPersonData() {
        return [
            ['FirstName 1', 'LastName 1', 'Name 1'],
            ['FirstName 1', 'LastName 1', null],
            ['FirstName 1', 'LastName 1', 'Name 1'],
            ['FirstName 1', 'LastName 1', null],
        ];
    }

    /**
     * @dataProvider findNewPersonData
     *
     * @param mixed $given
     * @param mixed $family
     * @param mixed $raceName
     */
    public function testFindNewPerson($given, $family, $raceName) : void {
        $found = $this->importer->findPerson($given, $family, $raceName);
        static::assertInstanceOf(Person::class, $found);

        static::assertSame(mb_convert_case($family, \MB_CASE_TITLE), $found->getLastName());
    }

    public function findNewPersonData() {
        return [
            ['Coup', 'McCity', 'indian'],
            ['Coup', 'McCity', 'non-indian'],
            ['Coup', 'McCity', null],
            ['Coup', 'McCity', 'indian'],
            ['Coup', 'McCity', null],
            ['Coup', 'McCity', 'non-indian'],
            // Unicode
            ['Coup', 'Cóup', null],
            ['Coup', 'чащах', null],
            ['Coup', 'şoföre', null],
        ];
    }

    public function testFindCity() : void {
        $cify = $this->importer->findCity('Name 1');
        static::assertInstanceOf(City::class, $cify);
        static::assertNotNull($cify->getId());
    }

    public function testNewFindCity() : void {
        $city = $this->importer->findCity('Name 5');
        static::assertInstanceOf(City::class, $city);
    }

    public function testFindRelationshipCategory() : void {
        $category = $this->importer->findRelationshipCategory('Name 1');
        static::assertInstanceOf(RelationshipCategory::class, $category);
        static::assertNotNull($category->getId());
    }

    public function testNewFindRelationshipCategory() : void {
        $category = $this->importer->findRelationshipCategory('Name 5');
        static::assertInstanceOf(RelationshipCategory::class, $category);
    }

    public function testFindTransactionCategory() : void {
        $category = $this->importer->findTransactionCategory('Label 1');
        static::assertInstanceOf(TransactionCategory::class, $category);
        static::assertNotNull($category->getId());
    }

    public function testNewFindTransactionCategory() : void {
        $category = $this->importer->findTransactionCategory('Label 5');
        static::assertInstanceOf(TransactionCategory::class, $category);
    }

    public function testNewFindRelationshipCategoryChars() : void {
        $category = $this->importer->findTransactionCategory('Jerk Nut');
        static::assertInstanceOf(TransactionCategory::class, $category);
        static::assertSame('jerk-nut', $category->getName());
    }

    public function testCreateTransaction() : void {
        $ledger = $this->getReference('ledger.1');
        $first = $this->getReference('person.1');
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
        static::assertInstanceOf(Transaction::class, $transaction);
    }

    /**
     * @dataProvider parseDateData
     *
     * @param mixed $expected
     * @param mixed $data
     */
    public function testParseDate($expected, $data) : void {
        $result = $this->importer->parseDate($data);
        static::assertSame($expected, $result);
    }

    public function parseDateData() {
        return [
            [null, null],
            [null, false],
            [null, ''],
            [null, 0],
            ['1790-03-01', '01 Mar 1790'],
            ['1790-03-01', '01  mar   1790'],
            ['1790-03-01', '01mar1790'],
            ['1790-03-01', '01 MAR 1790'],
            ['1790-03-01', '01 mar 1790'],
            ['1790-02-01', '1 Feb 1790'],
            ['1790-02-00', 'Feb 1790'],
            ['1790-02-00', '     Feb    1790 '],
            ['1790-02-00', 'FEB 1790'],
            ['1790-02-00', 'feb 1790'],
            ['1790-00-00', '1790'],
            ['1790-00-00', '  1790  '],
            ['1790-03-01', 'ca 01 Mar 1790'],
            ['1790-03-01', 'abt 01  mar   1790'],
            ['1790-03-01', 'bef 01mar1790'],
            ['1790-03-01', 'abt 01 MAR 1790'],
            ['1790-03-01', 'aft 01 mar 1790'],
            ['1777-00-00', 'bef 1777?'],
            ['1749-00-00', '1749?'],
            ['1767-12-30', '30 Dec 1767?'],
            ['1818-06-00', '[13?] Jun 1818'],
        ];
    }

    public function testFindLocationCategory() : void {
        $category = $this->importer->findLocationCategory('Name 1');
        static::assertInstanceOf(LocationCategory::class, $category);
        static::assertNotNull($category->getId());
    }

    public function testNewFindLocationCategory() : void {
        $category = $this->importer->findLocationCategory('Name 5');
        static::assertInstanceOf(LocationCategory::class, $category);
    }

    public function testFindLocation() : void {
        $location = $this->importer->findLocation('Name 1', 'Name 1');
        static::assertInstanceOf(Location::class, $location);
        static::assertNotNull($location->getId());
    }

    public function testNewFindLocation() : void {
        $location = $this->importer->findLocation('Saint Johns Church', 'church');
        static::assertInstanceOf(Location::class, $location);
    }

    public function testAddNullManumission() : void {
        $person = $this->getReference('person.1');
        $event = $this->importer->addManumission($person, []);
        static::assertNull($event);
    }

    public function testAddManumission() : void {
        $person = $this->getReference('person.1');
        $row = [
            7 => '5 Jun 1771',
        ];
        $event = $this->importer->addManumission($person, $row, 'Name 1');
        static::assertNotNull($event);
        static::assertSame('1771-06-05', $event->getDate());
        static::assertSame('5 Jun 1771', $event->getWrittenDate());
    }

    public function testAddNullBaptism() : void {
        $person = $this->getReference('person.1');
        $event = $this->importer->addBaptism($person, []);
        static::assertNull($event);
    }

    // Test commented out pending clarification from PI
//    public function testAddBaptism() : void {
//        $person = $this->getReference('person.1');
//        $row = [
//            5 => '5 Jun 1771',
//        ];
//        $event = $this->importer->addBaptism($person, $row, 'Name 2');
//        $this->assertNotNull($event);
//        $this->assertSame('1771-06-05', $event->getDate());
//        $this->assertSame('5 Jun 1771', $event->getWrittenDate());
//    }

    public function testAddNullResidence() : void {
        $person = $this->getReference('person.1');
        $residences = $person->getResidences()->count();
        $this->importer->addResidence($person, []);
        static::assertSame($residences, $person->getResidences()->count());
    }

    public function testAddResidence() : void {
        $person = $this->getReference('person.1');
        $residences = $person->getResidences()->count();
        $row = [
            9 => '5 Jun 1771',
            10 => 'Chicago',
        ];
        $this->importer->addResidence($person, $row);
        static::assertSame($residences + 1, $person->getResidences()->count());
    }

    public function testAddNullAliases() : void {
        $person = $this->getReference('person.1');
        $aliases = count($person->getAlias());
        $this->importer->addAliases($person, []);
        static::assertSame($aliases, count($person->getAlias()));
    }

    public function testAddAliases() : void {
        $person = $this->getReference('person.1');
        $aliases = count($person->getAlias());
        $row = [
            11 => 'Driver, cheese maker',
        ];
        $this->importer->addAliases($person, $row);
        static::assertSame($aliases + 2, count($person->getAlias()));
    }

    public function testSetNullNative() : void {
        $person = $this->getReference('person.1');
        $native = $person->getNative();
        $this->importer->setNative($person, []);
        static::assertSame($native, $person->getNative());
    }

    public function testSetNative() : void {
        $person = $this->getReference('person.1');
        $row = [
            12 => 'Chicago',
        ];
        $this->importer->setNative($person, $row);
        static::assertSame('Chicago', $person->getNative());
    }

    public function testAddNullOccupations() : void {
        $person = $this->getReference('person.1');
        $occupations = count($person->getOccupation());
        $this->importer->addOccupations($person, []);
        static::assertSame($occupations, count($person->getOccupation()));
    }

    public function testAddOccupations() : void {
        $person = $this->getReference('person.1');
        $occupations = count($person->getOccupation());
        $row = [
            13 => 'Driver; cheese maker',
        ];
        $this->importer->addOccupations($person, $row);
        static::assertSame($occupations + 2, count($person->getOccupation()));
    }

    protected function setUp() : void {
        parent::setUp();
        $this->importer = $this->getContainer()->get(ImportService::class);
    }
}
