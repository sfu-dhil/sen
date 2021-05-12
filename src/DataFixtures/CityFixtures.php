<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class CityFixtures extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $neworleans = new City();
        $neworleans->setName('Abbeville');
        $manager->persist($neworleans);
        $this->setReference('city.1', $neworleans);

        $bernard = new City();
        $bernard->setName('Saint Bernard Parish');
        $manager->persist($bernard);
        $this->setReference('city.2', $bernard);

        $manager->flush();
    }
}
