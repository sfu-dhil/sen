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

    protected function setUp() : void {
        parent::setUp();
        $this->importer = $this->getContainer()->get(ImportService::class);
    }
}
