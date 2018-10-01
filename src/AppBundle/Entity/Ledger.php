<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    public function __construct() {
        parent::__construct();
        $this->transactions = new ArrayCollection();
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
     * Set notary.
     *
     * @param Notary|null $notary
     *
     * @return Ledger
     */
    public function setNotary(Notary $notary = null) {
        $this->notary = $notary;

        return $this;
    }

    /**
     * Get notary.
     *
     * @return Notary|null
     */
    public function getNotary() {
        return $this->notary;
    }

    /**
     * Add transaction.
     *
     * @param Transaction $transaction
     *
     * @return Ledger
     */
    public function addTransaction(Transaction $transaction) {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction.
     *
     * @param Transaction $transaction
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTransaction(Transaction $transaction) {
        return $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions.
     *
     * @return Collection
     */
    public function getTransactions() {
        return $this->transactions;
    }
}
