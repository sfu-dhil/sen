<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class RaceFixtures extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $race = new Race();
        $race->setName('indian');
        $race->setLabel('Indian');
        $this->setReference('race.1', $race);
        $manager->persist($race);

        $manager->flush();
    }
}
