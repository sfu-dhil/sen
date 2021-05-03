<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadAll.
 *
 * @author mjoyce
 */
class AllFixtures extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) : void {
    }

    public function getDependencies() : array {
        return [
            CityFixtures::class,
            EventFixtures::class,
            EventCategoryFixtures::class,
            LedgerFixtures::class,
            LocationFixtures::class,
            LocationCategoryFixtures::class,
            NotaryFixtures::class,
            PersonFixtures::class,
            RaceFixtures::class,
            RelationshipFixtures::class,
            RelationshipCategoryFixtures::class,
            ResidenceFixtures::class,
            TransactionFixtures::class,
            TransactionCategoryFixtures::class,
            WitnessFixtures::class,
            WitnessCategoryFixtures::class,
            PageFixtures::class,
            PostFixtures::class,
            PostCategoryFixtures::class,
            PostStatusFixtures::class,
            ElementFixtures::class,
            CommentFixtures::class,
            CommentNoteFixtures::class,
            CommentStatusFixtures::class,
            UserFixtures::class,
        ];
    }
}
