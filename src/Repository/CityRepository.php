<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|City find($id, $lockMode = null, $lockVersion = null)
 * @method City[] findAll()
 * @method City[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|City findOneBy(array $criteria, array $orderBy = null)
 */
class CityRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, City::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('city')
            ->orderBy('city.name')
            ->getQuery();
    }

    /**
     * @return City[]|Collection
     */
    public function typeaheadQuery(string $q) {
        throw new \RuntimeException('Not implemented yet.');
        $qb = $this->createQueryBuilder('city');
        $qb->andWhere('city.column LIKE :q');
        $qb->orderBy('city.column', 'ASC');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    public function searchNameQuery(string $q) : Query {
        $qb = $this->createQueryBuilder('city');
        $qb->addSelect('MATCH (city.name) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}
