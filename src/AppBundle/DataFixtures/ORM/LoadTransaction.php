<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadTransaction extends Fixture implements DependentFixtureInterface
{
    //put your code here
    public function load(ObjectManager $manager) {
        $transaction = new Transaction();
        $transaction->setCategory($this->getReference("transactioncategory.1"));
        $transaction->setConjunction("to");
        $transaction->setFirstParty($this->getReference("person.1"));
        $transaction->setFirstPartyNote("and wife");
        $transaction->setLedger($this->getReference("ledger.1"));
        $transaction->setSecondParty($this->getReference("person.2"));
        $transaction->setSecondPartyNote("and children");
        $transaction->setDate(new \DateTime("1790-04-20"));
        $transaction->setPage(27);
        $manager->persist($transaction);
        $this->setReference("transaction.1", $transaction);
        $manager->flush();
    }

    public function getDependencies(): array {
        return array(
            LoadTransactionCategory::class,
            LoadLedger::class,
            LoadPerson::class,
        );
    }

}
