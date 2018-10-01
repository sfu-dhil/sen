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
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }
}
