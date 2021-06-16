<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Person;
use App\Entity\Relationship;
use App\Entity\RelationshipCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method null|Relationship find($id, $lockMode = null, $lockVersion = null)
 * @method Relationship[] findAll()
 * @method Relationship[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Relationship findOneBy(array $criteria, array $orderBy = null)
 */
class RelationshipRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Relationship::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('relationship')
            ->orderBy('relationship.id')
            ->getQuery();
    }

    /**
     * @param mixed $relationshipName
     * @param mixed $relationName
     *
     * @throws Exception
     */
    public function findRelationship(?Person $person, ?Person $relation, $relationshipName, $relationName) : ?Relationship {
        if ( ! $person || ! $relation) {
            return null;
        }
        $relationshipCategory = $this->getEntityManager()->getRepository(RelationshipCategory::class)->findOneBy(['name' => $relationshipName]);
        if ( ! $relationshipCategory) {
            throw new Exception("Relationship category '{$relationshipName}' is missing.");
        }
        if ($relationship = $this->findOneBy(['person' => $person, 'relation' => $relation, 'category' => $relationshipCategory])) {
            return $relationship;
        }

        $relationCategory = $this->getEntityManager()->getRepository(RelationshipCategory::class)->findOneBy(['name' => $relationName]);
        if ( ! $relationCategory) {
            throw new Exception("Relationship category '{$relationName}' is missing.");
        }
        if ($relationship = $this->findOneBy(['person' => $relation, 'relation' => $person, 'category' => $relationCategory])) {
            return $relationship;
        }

        return null;
    }
}
