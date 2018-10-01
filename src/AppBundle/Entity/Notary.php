<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var Collection|Ledger[]
     * @ORM\OneToMany(targetEntity="Ledger", mappedBy="notary")
     */
    private $ledgers;

    public function __construct() {
        parent::__construct();
        $this->ledgers = new ArrayCollection();
    }

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }

    /**
     * Add ledger.
     *
     * @param Ledger $ledger
     *
     * @return Notary
     */
    public function addLedger(Ledger $ledger)
    {
        $this->ledgers[] = $ledger;

        return $this;
    }

    /**
     * Remove ledger.
     *
     * @param Ledger $ledger
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLedger(Ledger $ledger)
    {
        return $this->ledgers->removeElement($ledger);
    }

    /**
     * Get ledgers.
     *
     * @return Collection
     */
    public function getLedgers()
    {
        return $this->ledgers;
    }
}
