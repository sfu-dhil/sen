<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Witness
 *
 * @ORM\Table(name="witness")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WitnessRepository")
 */
class Witness extends AbstractEntity
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
