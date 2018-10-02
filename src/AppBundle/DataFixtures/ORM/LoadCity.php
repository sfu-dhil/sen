<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadCity extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) {
        $neworleans = new City();
        $neworleans->setName("New Orleans");
        $manager->persist($neworleans);
        $this->setReference("city.1", $neworleans);

        $bernard = new City();
        $bernard->setName("Saint Bernard Parish");
        $manager->persist($bernard);
        $this->setReference("city.2", $bernard);

        $manager->flush();
    }

}
