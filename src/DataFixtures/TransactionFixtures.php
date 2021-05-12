<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Transaction;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class TransactionFixtures extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $transaction = new Transaction();
        $transaction->setCategory($this->getReference('transactioncategory.1'));
        $transaction->setConjunction('to');
        $transaction->setFirstParty($this->getReference('person.1'));
        $transaction->setFirstPartyNote('and wife');
        $transaction->setLedger($this->getReference('ledger.1'));
        $transaction->setSecondParty($this->getReference('person.2'));
        $transaction->setSecondPartyNote('and children');
        $transaction->setDate(new DateTimeImmutable('1790-04-20'));
        $transaction->setPage(27);
        $manager->persist($transaction);
        $this->setReference('transaction.1', $transaction);
        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            TransactionCategoryFixtures::class,
            LedgerFixtures::class,
            PersonFixtures::class,
        ];
    }
}
