<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\BirthStatus;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|BirthStatus find($id, $lockMode = null, $lockVersion = null)
 * @method BirthStatus[] findAll()
 * @method BirthStatus[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|BirthStatus findOneBy(array $criteria, array $orderBy = null)
 */
class BirthStatusRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, BirthStatus::class);
    }
}
