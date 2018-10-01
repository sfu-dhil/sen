<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Ledger
 *
 * @ORM\Table(name="ledger")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LedgerRepository")
 */
class Ledger extends AbstractEntity
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
