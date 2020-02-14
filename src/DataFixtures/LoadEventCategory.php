<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\EventCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class EventCategoryFixtures extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $baptism = new EventCategory();
        $baptism->setName('baptism');
        $baptism->setLabel('Baptism');
        $baptism->setDescription('Baptism is a rite of admission into Christianity.');
        $manager->persist($baptism);
        $this->setReference('eventcategory.1', $baptism);

        $manumission = new EventCategory();
        $manumission->setName('manumission');
        $manumission->setLabel('Manumission');
        $manumission->setDescription('Manumission, or affranchisement, is the act of an owner freeing his or her slaves.');
        $manager->persist($manumission);
        $this->setReference('eventcategory.2', $manumission);

        $manager->flush();
    }
}
