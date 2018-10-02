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
class LoadLocationCategory extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) {
        $category = new EventCategory();
        $category->setName('church');
        $category->setLabel('Church');
        $manager->persist($category);
        $this->setReference('category.1', $category);

        $manager->flush();
    }

}
