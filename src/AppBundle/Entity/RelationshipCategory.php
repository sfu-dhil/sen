<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * RelationshipCategory
 *
 * @ORM\Table(name="relationship_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RelationshipCategoryRepository")
 */
class RelationshipCategory extends AbstractTerm
{

    /**
     * @var Collection|Relationship[]
     * @ORM\OneToMany(targetEntity="Relationship", mappedBy="category")
     */
    private $relationships;

    public function __construct() {
        parent::__construct();
        $this->relationships = new ArrayCollection();
    }

    /**
     * Add relationship.
     *
     * @param Relationship $relationship
     *
     * @return RelationshipCategory
     */
    public function addRelationship(Relationship $relationship)
    {
        $this->relationships[] = $relationship;

        return $this;
    }

    /**
     * Remove relationship.
     *
     * @param Relationship $relationship
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRelationship(Relationship $relationship)
    {
        return $this->relationships->removeElement($relationship);
    }

    /**
     * Get relationships.
     *
     * @return Collection
     */
    public function getRelationships()
    {
        return $this->relationships;
    }
}
