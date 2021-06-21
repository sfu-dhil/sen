<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Repository\RelationshipRepository;
use App\Util\NotaryColumnDefinitions as N;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Command\Command;

/**
 * AppImportNotaryCommand command.
 */
class ImportNotaryCommand extends AbstractImportCommand {
    private RelationshipRepository $relationshipRepository;

    protected static $defaultName = 'sen:import:notary';

    /**
     * @param mixed $row
     *
     * @throws Exception
     */
    protected function process($row) : void {
        $date = new DateTimeImmutable($row[N::transaction_date]);
        $notary = $this->importer->findNotary($row[N::notary_name]);
        $ledger = $this->importer->findLedger($notary, $row[N::ledger_volume], (int) $date->format('Y'));

        $firstParty = $this->importer->findPerson(
            $row[N::first_party_first_name],
            $row[N::first_party_last_name],
            $row[N::first_party_race],
            $row[N::first_party_sex]
        );
        $firstParty->addStatus($row[N::first_party_status]);

        $secondParty = $this->importer->findPerson(
            $row[N::second_party_first_name],
            $row[N::second_party_last_name],
            $row[N::second_party_race],
            $row[N::second_party_sex]
        );
        if($secondParty) {
            $secondParty->addStatus($row[N::second_party_status]);
        }

        if ($row[N::first_party_spouse]) {
            $firstSpouse = $this->importer->findPerson('', $row[N::first_party_spouse], null, $this->importer->otherSex($row[N::first_party_sex]));
            if ( ! $this->relationshipRepository->findRelationship($firstParty, $firstSpouse, 'spouse', 'spouse')) {
                $this->importer->addSpouse($firstParty, $row, $firstSpouse);
            }
        }
        if ($row[N::second_party_spouse]) {
            $secondSpouse = $this->importer->findPerson('', $row[N::second_party_spouse], null, $this->importer->otherSex($row[N::second_party_sex]));
            if ( ! $this->relationshipRepository->findRelationship($secondParty, $secondSpouse, 'spouse', 'spouse')) {
                $this->importer->addSpouse($secondParty, $row, $secondSpouse);
            }
        }
        $this->importer->createTransaction($ledger, $firstParty, $secondParty, $row);
    }

    /**
     * @required
     */
    public function setRelationshipRepository(RelationshipRepository $repo) : void {
        $this->relationshipRepository = $repo;
    }
}
