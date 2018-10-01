<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Notary
 *
 * @ORM\Table(name="notary")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotaryRepository")
 */
class Notary extends AbstractEntity
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
