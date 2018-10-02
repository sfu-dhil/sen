<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\RelationshipCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadRelationshipCategory extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) {
        $category = new RelationshipCategory();
        $category->setName('rel');
        $category->setLabel('Rel');
        $manager->persist($category);
        $this->setReference("relationshipcategory.1");
        
        $manager->flush();
    }

}
