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
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Ledger.
 *
 * @ORM\Table(name="ledger")
 * @ORM\Entity(repositoryClass="App\Repository\LedgerRepository")
 */
class Ledger extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", length=24, nullable=false)
     */
    private $volume;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $year;

    /**
     * @var Notary
     * @ORM\ManyToOne(targetEntity="Notary", inversedBy="ledgers")
     * @ORM\JoinColumn(nullable=false)
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
        return $this->notary . ' ' . $this->volume;
    }

    public function getName() {
        return $this->__toString();
    }

    /**
     * Set notary.
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
     * @return null|Notary
     */
    public function getNotary() {
        return $this->notary;
    }

    /**
     * Add transaction.
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
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
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

    /**
     * Set volume.
     *
     * @param string $volume
     *
     * @return Ledger
     */
    public function setVolume($volume) {
        $this->volume = $volume;

        return $this;
    }

    /**
     * Get volume.
     *
     * @return string
     */
    public function getVolume() {
        return $this->volume;
    }

    /**
     * Set year.
     *
     * @param int $year
     *
     * @return Ledger
     */
    public function setYear($year) {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear() {
        return $this->year;
    }
}
