<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Repository;

use App\DataFixtures\PersonFixtures;
use App\DataFixtures\RelationshipCategoryFixtures;
use App\DataFixtures\RelationshipFixtures;
use App\Entity\Person;
use App\Repository\RelationshipRepository;
use Exception;
use Nines\UtilBundle\Tests\ServiceBaseCase;

class RelationshipRepositoryTest extends ServiceBaseCase {
    private RelationshipRepository $repo;

    protected function fixtures() : array {
        return [
            PersonFixtures::class,
            RelationshipCategoryFixtures::class,
            RelationshipFixtures::class,
        ];
    }

    /**
     * @test
     */
    public function config() : void {
        $this->assertInstanceOf(RelationshipRepository::class, $this->repo);
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function findRelationship() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.3');
        $this->assertNotNull($this->repo->findRelationship($person, $relation, 'Name 1', 'Name 1'));
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function findRelation() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.3');
        // People 1 and 2 have relationships 1 and 3 but not 2.
        $this->assertNotNull($this->repo->findRelationship($person, $relation, 'Name 2', 'Name 3'));
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function findNoRelationship() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.2');
        $this->assertNull($this->repo->findRelationship($person, $relation, 'Name 1', 'Name 1'));
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @test
     */
    public function findRelationshipNameException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.2');
        $this->assertNull($this->repo->findRelationship($person, $relation, 'Name 1234', 'Name 1'));
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @test
     */
    public function findRelationNameException() : void {
        $this->expectException(Exception::class);
        /** @var Person $person */
        $person = $this->getReference('person.1');
        /** @var Person $relation */
        $relation = $this->getReference('person.2');
        $this->assertNull($this->repo->findRelationship($person, $relation, 'Name 1', 'Name 11332'));
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function findNullPersonRelationship() : void {
        /** @var Person $relation */
        $relation = $this->getReference('person.2');
        $this->assertNull($this->repo->findRelationship(null, $relation, 'Name 1', 'Name 1'));
    }

    /**
     * @throws Exception
     *
     * @test
     */
    public function findNullRelationRelationship() : void {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $this->assertNull($this->repo->findRelationship($person, null, 'Name 1', 'Name 1'));
    }

    /**
     * @throws Exception
     */
    protected function setUp() : void {
        parent::setUp();
        $repo = $this->getContainer()->get(RelationshipRepository::class);
        if ( ! $repo instanceof RelationshipRepository) {
            throw new Exception('Misconfigured service container.');
        }
        $this->repo = $repo;
    }
}
