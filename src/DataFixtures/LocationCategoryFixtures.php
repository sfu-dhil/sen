<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\LocationCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class LocationCategoryFixtures extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $category = new LocationCategory();
        $category->setName('church');
        $category->setLabel('Church');
        $manager->persist($category);
        $this->setReference('locationcategory.1', $category);

        $manager->flush();
    }
}
