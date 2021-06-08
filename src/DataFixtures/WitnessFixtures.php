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

class WitnessFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 1; $i <= 4; $i++) {
            $fixture = new Witness();

            $fixture->setCategory($this->getReference('witnesscategory.' . $i));
            $fixture->setPerson($this->getReference('person.' . $i));
            $fixture->setEvent($this->getReference('event.' . $i));
            $em->persist($fixture);
            $this->setReference('witness.' . $i, $fixture);
        }
        $em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies() {
        return [
            WitnessCategoryFixtures::class,
            PersonFixtures::class,
            EventFixtures::class,
        ];
    }
}
