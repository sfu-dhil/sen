<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PersonFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 1; $i <= 4; $i++) {
            $fixture = new Person();
            $fixture->setFirstName('FirstName ' . $i);
            $fixture->setLastName('LastName ' . $i);
            $fixture->setAlias(['Alias ' . $i]);
            $fixture->setNative('Native ' . $i);
            $fixture->setOccupation(['Occupation ' . $i]);
            $fixture->setSex($i % 2 ? 'M' : 'F');
            $fixture->setBirthDate('Born ' . $i);
            $fixture->setWrittenBirthDate('WrittenBirthDate ' . $i);
            $fixture->setBirthStatus('BirthStatus ' . $i);
            $fixture->setStatus('Status ' . $i);

            $fixture->setBirthplace($this->getReference('city.' . $i));
            $fixture->setRace($this->getReference('race.' . $i));
            $em->persist($fixture);
            $this->setReference('person.' . $i, $fixture);
        }
        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        return [
            CityFixtures::class,
            RaceFixtures::class,
        ];
    }
}
