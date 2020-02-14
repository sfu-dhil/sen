<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class PersonFixtures extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $person1 = new Person();
        $person1->setFirstName('Emery');
        $person1->setLastName('Ville');
        $person1->setNative('Attakapas');
        $person1->setBirthDate('1760-01-02');
        $person1->setBirthDateDisplay('2 Jan 1760');
        $person1->setBirthPlace($this->getReference('city.1'));
        $person1->setAlias(['Em', 'EV']);
        $person1->setOccupation('1775 soldier');
        $person1->setRace($this->getReference('race.1'));
        $person1->setSex(Person::MALE);
        $person1->setStatus('free');
        $manager->persist($person1);
        $this->setReference('person.1', $person1);

        $person2 = new Person();
        $person2->setFirstName('Savanah');
        $person2->setLastName('Kansas');
        $person1->setBirthDateDisplay('3 Feb 1760');
        $person2->setBirthDate('1761-02-03');
        $person2->setBirthPlace($this->getReference('city.1'));
        $person2->setOccupation('1776 busness person');
        $person2->setRace($this->getReference('race.1'));
        $person2->setSex(Person::FEMALE);
        $person2->setStatus('free');
        $manager->persist($person2);
        $this->setReference('person.2', $person2);

        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            CityFixtures::class,
            RaceFixtures::class,
        ];
    }
}
