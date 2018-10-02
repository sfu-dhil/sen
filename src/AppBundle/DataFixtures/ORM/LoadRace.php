<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadRace extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) {
        $race = new Race();
        $race->setName("indian");
        $race->setLabel("Indian");
        $this->setReference("race.1");
        $manager->persist($race);

        $manager->flush();
    }

}
