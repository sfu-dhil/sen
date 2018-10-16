<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Relationship
 *
 * @ORM\Table(name="relationship")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RelationshipRepository")
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
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(nullable=false)
     */
    private $relation;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return $this->person . " " . $this->category . " " . $this->relation;
    }


    /**
     * Set startDate.
     *
     * @param string|null $startDate
     *
     * @return Relationship
     */
    public function setStartDate($startDate = null)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return string|null
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param string|null $endDate
     *
     * @return Relationship
     */
    public function setEndDate($endDate = null)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return string|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set category.
     *
     * @param RelationshipCategory $category
     *
     * @return Relationship
     */
    public function setCategory(RelationshipCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return RelationshipCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set person.
     *
     * @param Person $person
     *
     * @return Relationship
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set relation.
     *
     * @param Person $relation
     *
     * @return Relationship
     */
    public function setRelation(Person $relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * Get relation.
     *
     * @return Person
     */
    public function getRelation()
    {
        return $this->relation;
    }
}
