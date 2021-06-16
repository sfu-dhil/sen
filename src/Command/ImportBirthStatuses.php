<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\BirthStatus;
use App\Repository\BirthStatusRepository;

class ImportBirthStatuses extends AbstractImportCommand {
    private BirthStatusRepository $repo;

    protected static $defaultName = 'sen:import:birth-status';

    protected function process(array $row) : void {
        $standard = mb_convert_case($row[0], MB_CASE_LOWER);
        $status = $this->repo->findOneBy(['name' => $standard]);
        if ( ! $status) {
            $status = new BirthStatus();
            $status->setName($standard);
            $status->setLabel(mb_convert_case($standard, MB_CASE_TITLE));
            $status->setDescription($row[1]);
            $this->em->persist($status);
        }
    }

    /**
     * @required
     */
    public function setBirthStatusRepository(BirthStatusRepository $repo) : void {
        $this->repo = $repo;
    }
}
