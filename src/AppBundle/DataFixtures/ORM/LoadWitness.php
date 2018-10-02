<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Witness;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadWitness extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) {
        $witness = new Witness();
        $witness->setCategory($this->getReference("witnesscategory.1"));
        $witness->setEvent($this->getReference("event.1"));
        $witness->setPerson($this->getReference("person.1"));
        $manager->persist($witness);
        $this->setReference("witness.1", $witness);

        $manager->flush();
    }

    public function getDependencies(): array {
        return array(
            LoadEvent::class,
            LoadWitnessCategory::class,
            LoadPerson::class,
        );
    }

}
