<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Util\NotaryColumnDefinitions as N;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Command\Command;

/**
 * AppImportNotaryCommand command.
 */
class ImportNotaryCommand extends AbstractImportCommand {
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

        $secondParty = $this->importer->findPerson(
            $row[N::second_party_first_name],
            $row[N::second_party_last_name],
            $row[N::second_party_race],
            $row[N::second_party_sex]
        );

        $this->importer->createTransaction($ledger, $firstParty, $secondParty, $row);
    }
}
