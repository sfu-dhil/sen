<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 */
class LoadLocation extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) {
        $location = new Location();
        $location->setCategory($this->getReference("locationcategory.1"));
        $location->setName("Saint Barnabas Church");
        $manager->persist($location);
        $this->setReference('location.1', $location);

        $manager->flush();
    }

    public function getDependencies(): array {
        return array(
            LoadLocationCategory::class,
        );
    }

}
