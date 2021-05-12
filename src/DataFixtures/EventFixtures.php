<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class EventFixtures extends Fixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager) : void {
        $event1 = new Event();
        $event1->setWrittenDate('21 Feb 1792');
        $event1->setDate('1792-02-21');
        $event1->setCategory($this->getReference('eventcategory.1'));
        $event1->setLocation($this->getReference('location.1'));
        $event1->setNote('Seen original.');
        $event1->addParticipant($this->getReference('person.1'));
        $this->setReference('event.1', $event1);
        $manager->persist($event1);

        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            CityFixtures::class,
            EventCategoryFixtures::class,
            LocationFixtures::class,
            PersonFixtures::class,
        ];
    }
}
