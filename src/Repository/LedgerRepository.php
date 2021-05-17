<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Ledger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Ledger find($id, $lockMode = null, $lockVersion = null)
 * @method null|Ledger findOneBy(array $criteria, array $orderBy = null)
 * @method Ledger[] findAll()
 * @method Ledger[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LedgerRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Ledger::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('ledger')
            ->orderBy('ledger.year', 'asc')
            ->orderBy('ledger.volume', 'asc')
            ->getQuery()
        ;
    }

    /**
     * @param string $q
     *
     * @return Collection|Ledger[]
     */
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('ledger');
        $qb->andWhere('ledger.year LIKE :q');
        $qb->orderBy('ledger.yar', 'ASC');
        $qb->orderBy('ledger.volume', 'asc');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}
