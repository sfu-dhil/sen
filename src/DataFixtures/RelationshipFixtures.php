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

class RelationshipFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 1; $i <= 4; $i++) {
            $fixture = new Relationship();
            $fixture->setStartDate('Date ' . $i);
            $fixture->setEndDate('Date ' . $i);

            $fixture->setCategory($this->getReference('relationshipcategory.' . $i));
            $fixture->setPerson($this->getReference('person.' . $i));
            $fixture->setRelation($this->getReference('person.' . (($i + 1) % 4 + 1)));
            $em->persist($fixture);
            $this->setReference('relationship.' . $i, $fixture);
        }
        $em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies() {
        return [
            RelationshipCategoryFixtures::class,
            PersonFixtures::class,
            PersonFixtures::class,
        ];
    }
}
