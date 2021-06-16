<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Repository;

use App\DataFixtures\EventCategoryFixtures;
use App\DataFixtures\EventFixtures;
use App\DataFixtures\PersonFixtures;
use App\DataFixtures\RelationshipCategoryFixtures;
use App\DataFixtures\RelationshipFixtures;
use App\Entity\EventCategory;
use App\Entity\Person;
use App\Repository\EventRepository;
use App\Repository\RelationshipRepository;
use Exception;
use Nines\UtilBundle\Tests\ServiceBaseCase;

class EventRepositoryTest extends ServiceBaseCase {
    private EventRepository $repo;

    protected function fixtures() : array {
        return [
            PersonFixtures::class,
            EventFixtures::class,
            EventCategoryFixtures::class,
        ];
    }

    /**
     * @test
     */
    public function config() : void {
        $this->assertInstanceOf(EventRepository::class, $this->repo);
    }

    public function testFindEvent() {
        /** @var Person $person */
        $person = $this->getReference('person.1');
        $events = $this->repo->findEvent('Name 1', $person);
        $this->assertCount(1, $events);
    }


    public function testFindNoEvent() {
        /** @var Person $person */
        $person = $this->getReference('person.3');
        $events = $this->repo->findEvent('Name 1', $person);
        $this->assertCount(0, $events);
    }

    /**
     * @throws Exception
     */
    protected function setUp() : void {
        parent::setUp();
        $repo = $this->getContainer()->get(EventRepository::class);
        if ( ! $repo instanceof EventRepository) {
            throw new Exception('Misconfigured service container.');
        }
        $this->repo = $repo;
    }
}
