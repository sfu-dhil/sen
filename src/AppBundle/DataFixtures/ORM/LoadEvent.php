<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadEvent extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $event1 = new Event();
        $event1->setWrittenDate("21 Feb 1792");
        $event1->setDate("1792-02-21");
        $event1->setCategory($this->getReference("eventcategory.1"));
        $event1->setLocation($this->getReference("location.1"));
        $event1->setNote("Seen original.");
        $event1->addParticipant($this->getReference('person.1'));
        $this->setReference("event.1", $event1);
        $manager->persist($event1);

        $manager->flush();
    }

    public function getDependencies(): array {
        return array(
            LoadCity::class,
            LoadEventCategory::class,
            LoadLocation::class,
            LoadPerson::class,
        );
    }

}
