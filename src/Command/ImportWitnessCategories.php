<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\WitnessCategory;
use App\Repository\WitnessCategoryRepository;

class ImportWitnessCategories extends AbstractImportCommand {
    private WitnessCategoryRepository $repo;

    protected static $defaultName = 'sen:import:witness-categories';

    protected function process(array $row) : void {
        $standard = $row[0];
        $category = $this->repo->findOneBy(['name' => $standard]);
        if ( ! $category) {
            $category = new WitnessCategory();
            $category->setName($standard);
            $category->setLabel(mb_convert_case($standard, MB_CASE_TITLE));
            $this->em->persist($category);
            $this->em->flush();
        }
    }

    /**
     * @required
     */
    public function setTransactionCategoryRepository(WitnessCategoryRepository $repo) : void {
        $this->repo = $repo;
    }
}
