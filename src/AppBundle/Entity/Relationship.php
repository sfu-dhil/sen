<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
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
     * @var RelationshipCategory
     * @ORM\ManyToOne(targetEntity="RelationshipCategory", inversedBy="relationships")
     */
    private $category;

    /**
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="relationships")
     */
    private $people;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }

    /**
     * Set category.
     *
     * @param RelationshipCategory|null $category
     *
     * @return Relationship
     */
    public function setCategory(RelationshipCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return RelationshipCategory|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add person.
     *
     * @param Person $person
     *
     * @return Relationship
     */
    public function addPerson(Person $person)
    {
        $this->people[] = $person;

        return $this;
    }

    /**
     * Remove person.
     *
     * @param Person $person
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePerson(Person $person)
    {
        return $this->people->removeElement($person);
    }

    /**
     * Get people.
     *
     * @return Collection
     */
    public function getPeople()
    {
        return $this->people;
    }
}
