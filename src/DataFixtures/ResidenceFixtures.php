<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Residence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class ResidenceFixtures extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) : void {
        $residence = new Residence();
        $residence->setCity($this->getReference('city.1'));
        $residence->setPerson($this->getReference('person.1'));
        $residence->setDate('1780');
        $manager->persist($residence);
        $this->setReference('residence.1', $residence);

        $manager->flush();
    }

    public function getDependencies() : array {
        return [
            CityFixtures::class,
            PersonFixtures::class,
        ];
    }
}
