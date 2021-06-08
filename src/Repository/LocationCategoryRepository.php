<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\LocationCategory;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|LocationCategory find($id, $lockMode = null, $lockVersion = null)
 * @method LocationCategory[] findAll()
 * @method LocationCategory[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|LocationCategory findOneBy(array $criteria, array $orderBy = null)
 */
class LocationCategoryRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, LocationCategory::class);
    }
}
