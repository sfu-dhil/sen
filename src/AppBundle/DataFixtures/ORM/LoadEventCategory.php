<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\EventCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadEventCategory extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) {
        $baptism = new EventCategory();
        $baptism->setName('baptism');
        $baptism->setLabel('Baptism');
        $baptism->setDescription('Baptism is a rite of admission into Christianity.');
        $manager->persist($baptism);
        $this->setReference('eventcategory.1', $baptism);

        $manumission = new EventCategory();
        $manumission->setName('manumission');
        $manumission->setLabel('Manumission');
        $manumission->setDescription('Manumission, or affranchisement, is the act of an owner freeing his or her slaves.');
        $manager->persist($manumission);
        $this->setReference('eventcategory.2', $manumission);
    }

}
