<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Witness;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class WitnessFixtures extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $witness = new Witness();
        $witness->setCategory($this->getReference('witnesscategory.1'));
        $witness->setEvent($this->getReference('event.1'));
        $witness->setPerson($this->getReference('person.1'));
        $manager->persist($witness);
        $this->setReference('witness.1', $witness);

        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            EventFixtures::class,
            WitnessCategoryFixtures::class,
            PersonFixtures::class,
        ];
    }
}
