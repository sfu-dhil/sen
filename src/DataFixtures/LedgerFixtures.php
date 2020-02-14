<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Ledger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class LedgerFixtures extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $ledger = new Ledger();
        $ledger->setNotary($this->getReference('notary.1'));
        $ledger->setVolume('9; 10');
        $ledger->setYear('1794');
        $manager->persist($ledger);
        $this->setReference('ledger.1', $ledger);

        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            NotaryFixtures::class,
        ];
    }
}
