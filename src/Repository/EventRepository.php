<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Event find($id, $lockMode = null, $lockVersion = null)
 * @method Event[] findAll()
 * @method Event[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Event findOneBy(array $criteria, array $orderBy = null)
 */
class EventRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Event::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('event')
            ->orderBy('event.id')
            ->getQuery();
    }

    public function typeaheadQuery($q) {
        return $this->createQueryBuilder('event')
            ->where('event.date LIKE :q')
            ->setParameter('q', $q . '%')
            ->orderBy('event.id')
            ->getQuery();
    }

    public function findEvent(string $categoryName, Person ...$people) {
        $category = $this->_em->getRepository(EventCategory::class)->findOneBy([
            'name' => $categoryName,
        ]);

        $qb = $this->createQueryBuilder('event');
        $qb->where('event.category = :category');
        $qb->setParameter('category', $category);

        $qb->innerJoin('event.participants', 'p', Join::WITH, 'p.id IN (:ids)');
        $qb->setParameter('ids', array_map(fn(Person $p) => $p->getId(), $people));

        return $qb->getQuery()->execute();
    }

}
