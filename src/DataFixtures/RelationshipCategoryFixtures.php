<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\RelationshipCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class RelationshipCategoryFixtures extends Fixture {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $category = new RelationshipCategory();
        $category->setName('rel');
        $category->setLabel('Rel');
        $manager->persist($category);
        $this->setReference('relationshipcategory.1', $category);

        $manager->flush();
    }
}
