<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Relationship;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class RelationshipFixtures extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $relationship = new Relationship();
        $relationship->setCategory($this->getReference('relationshipcategory.1'));
        $relationship->setPerson($this->getReference('person.1'));
        $relationship->setRelation($this->getReference('person.2'));
        $manager->persist($relationship);
        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            PersonFixtures::class,
            RelationshipCategoryFixtures::class,
        ];
    }
}
