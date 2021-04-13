<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 */
class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    //put your code here
    public function load(ObjectManager $manager) : void {
        $location = new Location();
        $location->setCategory($this->getReference('locationcategory.1'));
        $location->setName('Saint Barnabas Church');
        $manager->persist($location);
        $this->setReference('location.1', $location);

        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            LocationCategoryFixtures::class,
        ];
    }
}
