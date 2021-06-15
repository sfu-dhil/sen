<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Race;
use App\Repository\RaceRepository;

class ImportRaceCommand extends AbstractImportCommand {
    private RaceRepository $repo;

    protected static $defaultName = 'sen:import:race';

    protected function append(?string $s, ?string $t) : ?string {
        if ( ! $t) {
            return $s;
        }
        if ($s) {
            return $s . '/' . $t;
        }

        return $t;
    }

    protected function process($row) : void {
        $standard = $row[0];
        $race = $this->repo->findOneBy(['name' => $standard]);
        if ( ! $race) {
            $race = new Race();
            $race->setName($standard);
            $race->setLabel(mb_convert_case($standard, MB_CASE_TITLE));
            $this->em->persist($race);
        }
        $race->setSpanishUngendered($this->append($race->getSpanishUngendered(), $row[1]));
        $race->setSpanishMale($this->append($race->getSpanishMale(), $row[2]));
        $race->setSpanishFemale($this->append($race->getSpanishFemale(), $row[3]));
        $race->setFrenchUngendered($this->append($race->getFrenchUngendered(), $row[4]));
        $race->setFrenchMale($this->append($race->getFrenchMale(), $row[5]));
        $race->setFrenchFemale($this->append($race->getFrenchFemale(), $row[6]));
    }

    /**
     * @required
     */
    public function setRaceRepository(RaceRepository $repo) : void {
        $this->repo = $repo;
    }
}
