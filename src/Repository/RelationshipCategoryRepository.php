<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\RelationshipCategory;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|RelationshipCategory find($id, $lockMode = null, $lockVersion = null)
 * @method RelationshipCategory[] findAll()
 * @method RelationshipCategory[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|RelationshipCategory findOneBy(array $criteria, array $orderBy = null)
 */
class RelationshipCategoryRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, RelationshipCategory::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('v')
            ->orderBy('v.weight')
            ->addOrderBy('v.label')
            ->getQuery()
            ;
    }
}
