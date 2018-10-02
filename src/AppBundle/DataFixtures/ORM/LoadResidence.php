<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Residence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadResidence extends Fixture implements DependentFixtureInterface
{
    //put your code here
    public function load(ObjectManager $manager) {
        $residence = new Residence();
        $residence->setCity($this->getReference("city.1"));
        $residence->setPerson($this->getReference("person.1"));
        $residence->setDate("1780");
        $manager->persist($residence);
        $this->setReference("residence.1", $residence);

        $manager->flush();
    }

    public function getDependencies(): array {
        return array(
            LoadCity::class,
            LoadPerson::class,
        );
    }

}
