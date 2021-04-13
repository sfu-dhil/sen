<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * RelationshipCategory.
 *
 * @ORM\Table(name="relationship_category")
 * @ORM\Entity(repositoryClass="App\Repository\RelationshipCategoryRepository")
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
     * @return RelationshipCategory
     */
    public function addRelationship(Relationship $relationship) {
        $this->relationships[] = $relationship;

        return $this;
    }

    /**
     * Remove relationship.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRelationship(Relationship $relationship) {
        return $this->relationships->removeElement($relationship);
    }

    /**
     * Get relationships.
     *
     * @return Collection
     */
    public function getRelationships() {
        return $this->relationships;
    }
}
