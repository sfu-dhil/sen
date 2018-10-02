<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TransactionCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadTransactionCategory extends Fixture {

    public function load(ObjectManager $manager) {
        $category = new TransactionCategory();
        $category->setName("sale-property");
        $category->setLabel("Sale of property");
        $manager->persist($category);
        $this->setReference("transactioncategory.1", $category);
        $manager->flush();
    }

}
