<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\City;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Person find($id, $lockMode = null, $lockVersion = null)
 * @method Person[] findAll()
 * @method Person[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Person findOneBy(array $criteria, array $orderBy = null)
 */
class PersonRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Person::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('person')
            ->orderBy('person.lastName', 'asc')
            ->addOrderBy('person.firstName', 'asc')
            ->getQuery();
    }

    /**
     * @return Collection|Person[]
     */
    public function typeaheadQuery(string $q) {
        $qb = $this->createQueryBuilder('person');
        $qb->andWhere('person.lastName LIKE :q');
        $qb->orderBy('person.lastName', 'asc');
        $qb->addOrderBy('person.firstName', 'asc');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @return Person[]|Collection|Query
     */
    public function searchQuery(string $q) {
        $qb = $this->createQueryBuilder('person');
        $qb->addSelect('MATCH (person.firstName, person.lastName) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->andHaving('score > 0');
        $qb->orderBy('score', 'DESC');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}
