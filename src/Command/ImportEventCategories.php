<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\EventCategory;
use App\Repository\EventCategoryRepository;

class ImportEventCategories extends AbstractImportCommand {
    private EventCategoryRepository $repo;

    protected static $defaultName = 'sen:import:event-categories';

    protected function process(array $row) : void {
        $standard = $row[0];
        $category = $this->repo->findOneBy(['name' => $standard]);
        if ( ! $category) {
            $category = new EventCategory();
            $category->setName($standard);
            $category->setLabel(mb_convert_case($standard, MB_CASE_TITLE));
            $this->em->persist($category);
        }
    }

    /**
     * @required
     */
    public function setEventCategoryRepository(EventCategoryRepository $repo) : void {
        $this->repo = $repo;
    }
}
