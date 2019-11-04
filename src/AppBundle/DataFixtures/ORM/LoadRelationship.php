<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Relationship;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadRelationship extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) {
        $relationship = new Relationship();
        $relationship->setCategory($this->getReference('relationshipcategory.1'));
        $relationship->setPerson($this->getReference('person.1'));
        $relationship->setRelation($this->getReference('person.2'));
        $manager->persist($relationship);
        $manager->flush();
    }

    public function getDependencies(): array {
        return array(
            LoadPerson::class,
            LoadRelationshipCategory::class,
        );
    }

}
