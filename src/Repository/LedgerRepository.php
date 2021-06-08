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
 * @method Ledger[] findAll()
 * @method Ledger[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Ledger findOneBy(array $criteria, array $orderBy = null)
 */
class LedgerRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Ledger::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('ledger')
            ->orderBy('ledger.year', 'asc')
            ->orderBy('ledger.volume', 'asc')
            ->getQuery();
    }

    /**
     * @return Collection|Ledger[]
     */
    public function typeaheadQuery(string $q) {
        $qb = $this->createQueryBuilder('ledger');
        $qb->andWhere('ledger.year LIKE :q');
        $qb->orderBy('ledger.yar', 'ASC');
        $qb->orderBy('ledger.volume', 'asc');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}
