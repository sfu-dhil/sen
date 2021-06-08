<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Notary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Notary find($id, $lockMode = null, $lockVersion = null)
 * @method Notary[] findAll()
 * @method Notary[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Notary findOneBy(array $criteria, array $orderBy = null)
 */
class NotaryRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Notary::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('notary')
            ->orderBy('notary.name')
            ->getQuery();
    }

    /**
     * @return Collection|Notary[]
     */
    public function typeaheadQuery(string $q) {
        $qb = $this->createQueryBuilder('notary');
        $qb->andWhere('notary.name LIKE :q');
        $qb->orderBy('notary.name', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}
