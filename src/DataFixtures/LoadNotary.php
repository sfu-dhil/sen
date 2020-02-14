<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Notary;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class NotaryFixtures extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $notary = new Notary();
        $notary->setName('Billy Terwilliger');
        $manager->persist($notary);
        $this->setReference('notary.1', $notary);

        $manager->flush();
    }
}
