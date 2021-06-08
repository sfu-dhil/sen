<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Transaction;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 1; $i <= 4; $i++) {
            $fixture = new Transaction();
            $fixture->setDate(new DateTime("2020-{$i}-{$i}"));
            $fixture->setPage($i);
            $fixture->setNotes('Notes ' . $i);
            $fixture->setFirstPartyNote('FirstPartyNote ' . $i);
            $fixture->setConjunction('Conjunction ' . $i);
            $fixture->setSecondPartyNote('SecondPartyNote ' . $i);

            $fixture->setFirstparty($this->getReference('person.' . $i));
            $fixture->setSecondparty($this->getReference('person.' . (($i + 1) % 4 + 1)));
            $fixture->setCategory($this->getReference('transactioncategory.' . $i));
            $fixture->setLedger($this->getReference('ledger.' . $i));
            $em->persist($fixture);
            $this->setReference('transaction.' . $i, $fixture);
        }
        $em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies() {
        return [
            PersonFixtures::class,
            PersonFixtures::class,
            TransactionCategoryFixtures::class,
            LedgerFixtures::class,
        ];
    }
}
