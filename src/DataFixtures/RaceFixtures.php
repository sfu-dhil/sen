<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RaceFixtures extends Fixture {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 1; $i <= 4; $i++) {
            $fixture = new Race();
            $fixture->setName('Name ' . $i);
            $fixture->setLabel('Label ' . $i);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setSpanishUngendered('SpanishUngendered ' . $i);
            $fixture->setSpanishMale('SpanishMale ' . $i);
            $fixture->setSpanishFemale('SpanishFemale ' . $i);
            $fixture->setFrenchUngendered('FrenchUngendered ' . $i);
            $fixture->setFrenchMale('FrenchMale ' . $i);
            $fixture->setFrenchFemale('FrenchFemale ' . $i);

            $em->persist($fixture);
            $this->setReference('race.' . $i, $fixture);
        }
        $em->flush();
    }
}
