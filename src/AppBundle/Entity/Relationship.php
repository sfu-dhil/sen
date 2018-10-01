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
}
