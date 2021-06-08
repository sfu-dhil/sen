<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 1; $i <= 4; $i++) {
            $fixture = new Event();
            $fixture->setWrittenDate('WrittenDate ' . $i);
            $fixture->setDate('Date ' . $i);
            $fixture->setNote('Note ' . $i);

            $fixture->setCategory($this->getReference('eventcategory.' . $i));
            $fixture->setLocation($this->getReference('location.' . $i));
            $em->persist($fixture);
            $this->setReference('event.' . $i, $fixture);
        }
        $em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies() {
        return [
            EventCategoryFixtures::class,
            LocationFixtures::class,
        ];
    }
}
