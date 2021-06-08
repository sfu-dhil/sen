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
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Ledger.
 *
 * @ORM\Table(name="ledger")
 * @ORM\Entity(repositoryClass="App\Repository\LedgerRepository")
 */
class Ledger extends AbstractEntity {
    /**
     * @ORM\Column(type="string", length=24, nullable=false)
     */
    private string $volume;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $year;

    /**
     * @ORM\ManyToOne(targetEntity="Notary", inversedBy="ledgers")
     * @ORM\JoinColumn(nullable=false)
     */
    private Notary $notary;

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
     */
    public function __toString() : string {
        return $this->notary . ' ' . $this->volume;
    }

    public function getVolume() : ?string {
        return $this->volume;
    }

    public function setVolume(string $volume) : self {
        $this->volume = $volume;

        return $this;
    }

    public function getYear() : ?int {
        return $this->year;
    }

    public function setYear(int $year) : self {
        $this->year = $year;

        return $this;
    }

    public function getNotary() : ?Notary {
        return $this->notary;
    }

    public function setNotary(?Notary $notary) : self {
        $this->notary = $notary;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions() : Collection {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction) : self {
        if ( ! $this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setLedger($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction) : self {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getLedger() === $this) {
                $transaction->setLedger(null);
            }
        }

        return $this;
    }
}
