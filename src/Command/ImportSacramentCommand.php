<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Util\SacramentColumnDefinitions as S;
use Exception;
use Symfony\Component\Console\Command\Command;

/**
 * AppImportSacramentCommand command.
 */
class ImportSacramentCommand extends AbstractImportCommand {
    protected static $defaultName = 'sen:import:sacrament';

    /**
     * @param mixed $row
     *
     * @throws Exception
     */
    protected function process($row) : void {
        $person = $this->importer->findPerson($row[S::first_name], $row[S::last_name], $row[S::race_id], $row[S::sex]);
        $this->importer->setWrittenRace($person, $row); // includes race_id;
        $this->importer->setStatus($person, $row);
        $this->importer->addManumission($person, $row);
        $this->importer->addAliases($person, $row);
        $this->importer->addOccupations($person, $row);
        $this->importer->setNative($person, $row);
        $this->importer->addBirth($person, $row);
        $this->importer->setBirthStatus($person, $row);
        $baptism = $this->importer->addBaptism($person, $row);
        $this->importer->addParents($person, $row);
        $this->importer->addGodParents($person, $row, $baptism);
        $marriage = $this->importer->addMarriage($person, $row);
        $this->importer->addSpouse($person, $row);
        $this->importer->addMarriageWitnesses($person, $row, $marriage);
        $this->importer->addDeath($person, $row);
        $this->importer->addResidences($person, $row);
        $person->setNotes($row[S::notes]);
    }
}
