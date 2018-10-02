<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Ledger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadLedger extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) {
        $ledger = new Ledger();
        $ledger->setNotary($this->getReference("notary.1"));
        $ledger->setVolume("9; 10");
        $ledger->setYear("1794");
        $manager->persist($ledger);
        $this->setReference("ledger.1", $ledger);

        $manager->flush();
    }

    public function getDependencies(): array {
        return array(
            LoadNotary::class,
        );
    }

}
