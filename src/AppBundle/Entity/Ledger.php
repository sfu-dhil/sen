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
     * @var Notary
     * @ORM\ManyToOne(targetEntity="Notary", inversedBy="ledgers")
     */
    private $notary;

    /**
     * @var Collection|Transaction[]
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="ledger")
     */
    private $transactions;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }
}
