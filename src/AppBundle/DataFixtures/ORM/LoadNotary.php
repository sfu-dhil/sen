<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Notary;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadNotary extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) {
        $notary = new Notary();
        $notary->setName("Billy Terwilliger");
        $manager->persist($notary);
        $this->setReference('notary.1', $notary);

        $manager->flush();
    }

}
