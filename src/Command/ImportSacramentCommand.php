<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Repository\RelationshipRepository;
use App\Util\SacramentColumnDefinitions as S;
use Exception;
use Symfony\Component\Console\Command\Command;

/**
 * AppImportSacramentCommand command.
 */
class ImportSacramentCommand extends AbstractImportCommand {
    private RelationshipRepository $relationshipRepo;

    protected static $defaultName = 'sen:import:sacrament';

    /**
     * @param mixed $row
     *
     * @throws Exception
     */
    protected function process($row) : void {
        $person = $this->importer->findPerson($row[S::first_name], $row[S::last_name]);
        $this->importer->setWrittenRace($person, $row); // includes race_id;
        $this->importer->setStatus($person, $row);
        $this->importer->addManumission($person, $row);
        $this->importer->addAliases($person, $row);
        $this->importer->addOccupations($person, $row);
        $this->importer->setNative($person, $row);
        $this->importer->addBirth($person, $row);
        $this->importer->setBirthStatus($person, $row);
        $this->importer->addBaptism($person, $row);
        $this->importer->addParents($person, $row);
        $this->importer->addGodParents($person, $row);
        $spouse = $this->importer->findPerson($row[S::spouse_first_name], $row[S::spouse_last_name]);
        if($spouse &&  ! $this->relationshipRepo->findRelationship($person, $spouse, 'spouse', 'spouse')) {
            $this->importer->addSpouse($person, $row, $person);
        }
        // check for a marriage event with participants $person and S::spouse_first_name, S::spouse_last_name
//        $this->importer->addMarriage($person, $row);
//        $this->importer->addMarriageWitnesses($person, $row);
        $this->importer->addDeath($person, $row);
        $this->importer->addResidences($person, $row);
        $person->setNotes($row[S::notes]);
    }

    /**
     * @required
     */
    public function setRelationshipRepo(RelationshipRepository $relationshipRepo) : void {
        $this->relationshipRepo = $relationshipRepo;
    }
}
