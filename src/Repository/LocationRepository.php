<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Location find($id, $lockMode = null, $lockVersion = null)
 * @method Location[] findAll()
 * @method Location[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Location findOneBy(array $criteria, array $orderBy = null)
 */
class LocationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Location::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('location')
            ->orderBy('location.name')
            ->getQuery();
    }

    /**
     * @return Collection|Location[]
     */
    public function typeaheadQuery(string $q) {
        $qb = $this->createQueryBuilder('location');
        $qb->andWhere('location.name LIKE :q');
        $qb->orderBy('location.name', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }
}
