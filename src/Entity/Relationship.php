<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
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
class Relationship extends AbstractEntity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $startDate;

    /**
     * @var string
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $endDate;

    /**
     * @var RelationshipCategory
     * @ORM\ManyToOne(targetEntity="RelationshipCategory", inversedBy="relationships")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="relationships")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="relations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $relation;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a string representation of this entity.
     */
    public function __toString() : string {
        return $this->person . ' ' . $this->category . ' ' . $this->relation;
    }

    /**
     * Set startDate.
     *
     * @param null|string $startDate
     *
     * @return Relationship
     */
    public function setStartDate($startDate = null) {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return null|string
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param null|string $endDate
     *
     * @return Relationship
     */
    public function setEndDate($endDate = null) {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return null|string
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Set category.
     *
     * @return Relationship
     */
    public function setCategory(RelationshipCategory $category) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return RelationshipCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set person.
     *
     * @return Relationship
     */
    public function setPerson(Person $person) {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return Person
     */
    public function getPerson() {
        return $this->person;
    }

    /**
     * Set relation.
     *
     * @return Relationship
     */
    public function setRelation(Person $relation) {
        $this->relation = $relation;

        return $this;
    }

    /**
     * Get relation.
     *
     * @return Person
     */
    public function getRelation() {
        return $this->relation;
    }
}
