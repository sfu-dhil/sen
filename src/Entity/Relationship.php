<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Relationship.
 *
 * @ORM\Table(name="relationship")
 * @ORM\Entity(repositoryClass="App\Repository\RelationshipRepository")
 */
class Relationship extends AbstractEntity {
    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $startDate = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $endDate = null;

    /**
     * @ORM\ManyToOne(targetEntity="RelationshipCategory", inversedBy="relationships")
     * @ORM\JoinColumn(nullable=false)
     */
    private RelationshipCategory $category;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="relationships")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $person;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="relations")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $relation;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a string representation of this entity.
     */
    public function __toString() : string {
        return $this->person . ' ' . $this->category . ' ' . $this->relation;
    }

    public function getStartDate() : ?string {
        return $this->startDate;
    }

    public function setStartDate(?string $startDate) : self {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate() : ?string {
        return $this->endDate;
    }

    public function setEndDate(?string $endDate) : self {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCategory() : ?RelationshipCategory {
        return $this->category;
    }

    public function setCategory(?RelationshipCategory $category) : self {
        $this->category = $category;

        return $this;
    }

    public function getPerson() : ?Person {
        return $this->person;
    }

    public function setPerson(?Person $person) : self {
        $this->person = $person;

        return $this;
    }

    public function getRelation() : ?Person {
        return $this->relation;
    }

    public function setRelation(?Person $relation) : self {
        $this->relation = $relation;

        return $this;
    }
}
