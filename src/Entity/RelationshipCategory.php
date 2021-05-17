<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
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
class RelationshipCategory extends AbstractTerm {
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
     * @return Collection|Relationship[]
     */
    public function getRelationships() : Collection {
        return $this->relationships;
    }

    public function addRelationship(Relationship $relationship) : self {
        if ( ! $this->relationships->contains($relationship)) {
            $this->relationships[] = $relationship;
            $relationship->setCategory($this);
        }

        return $this;
    }

    public function removeRelationship(Relationship $relationship) : self {
        if ($this->relationships->removeElement($relationship)) {
            // set the owning side to null (unless already changed)
            if ($relationship->getCategory() === $this) {
                $relationship->setCategory(null);
            }
        }

        return $this;
    }
}
