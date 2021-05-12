<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Event find($id, $lockMode = null, $lockVersion = null)
 * @method null|Event findOneBy(array $criteria, array $orderBy = null)
 * @method Event[] findAll()
 * @method Event[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Query
     */
    public function indexQuery() {
        return $this->createQueryBuilder('event')
            ->orderBy('event.id')
            ->getQuery()
        ;
    }

    public function typeaheadQuery($q) {
        return $this->createQueryBuilder('event')
            ->where('event.date LIKE :q')
            ->setParameter('q', $q . '%')
            ->orderBy('event.id')
            ->getQuery()
        ;
    }
}
