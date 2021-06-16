<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Repository\EventRepository;
use App\Repository\RelationshipRepository;
use App\Util\SacramentColumnDefinitions as S;
use Exception;
use Symfony\Component\Console\Command\Command;

/**
 * AppImportSacramentCommand command.
 */
class ImportSacramentCommand extends AbstractImportCommand {
    private RelationshipRepository $relationshipRepo;

    private EventRepository $eventRepository;

    protected static $defaultName = 'sen:import:sacrament';

    /**
     * @param mixed $row
     *
     * @throws Exception
     */
    protected function process($row) : void {
        $person = $this->importer->findPerson($row[S::first_name], $row[S::last_name], $row[S::race_id], $row[S::sex]);
        $this->importer->addTitles($person, $row);
        $this->importer->setWrittenRace($person, $row); // includes race_id;
        $this->importer->setStatus($person, $row);
        $this->importer->addManumission($person, $row);
        $this->importer->addAliases($person, $row);
        $this->importer->addOccupations($person, $row);
        $this->importer->setNative($person, $row);
        $this->importer->addBirth($person, $row);
        $this->importer->setBirthStatus($person, $row);

        $parents = $this->importer->addParents($person, $row);
        $godparents = $this->importer->addGodParents($person, $row);
        $baptism = $this->importer->addBaptism($person, $row);
        if($baptism) {
            $this->importer->addEventWitnesses($baptism, 'godparent', ...array_values($godparents));
            $this->importer->addEventWitnesses($baptism, 'parent', ...array_values($parents));
        }

        $spouse = $this->importer->findPerson($row[S::spouse_first_name], $row[S::spouse_last_name]);
        if ($spouse && ! $this->relationshipRepo->findRelationship($person, $spouse, 'spouse', 'spouse')) {
            $this->importer->addSpouse($person, $row, $spouse);
        }

        // @todo check for an existing marriage for $person, $spouse.
        // @todo update the definition of addMarriage() to take the two people.
        // $marriage = $this->importer->addMarriage($person, $spouse, $row);

        // @todo update the definition of addMarriageWitnesses() to take the event.
        // $this->importer->addMarriageWitnesses($marriage, $row);

        $this->importer->addDeath($person, $row);
        $this->importer->addResidences($person, $row);
        $person->setNotes($row[S::notes]);
    }

    /**
     * @required
     */
    public function setEventRepository(EventRepository $eventRepository) : void {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @required
     */
    public function setRelationshipRepo(RelationshipRepository $relationshipRepo) : void {
        $this->relationshipRepo = $relationshipRepo;
    }
}
