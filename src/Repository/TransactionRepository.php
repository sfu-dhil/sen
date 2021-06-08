<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Transaction find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction[] findAll()
 * @method Transaction[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Transaction findOneBy(array $criteria, array $orderBy = null)
 */
class TransactionRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Transaction::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('transaction')
            ->orderBy('transaction.date')
            ->getQuery();
    }
}
