<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Transaction.
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction extends AbstractEntity {
    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private DateTime $date;

    /**
     * @ORM\Column(type="integer")
     */
    private int $page;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $notes;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="firstPartyTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $firstParty;

    /**
     * @ORM\Column(type="string")
     */
    private string $firstPartyNote;

    /**
     * @ORM\Column(type="string")
     */
    private string $conjunction;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="secondPartyTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $secondParty;

    /**
     * @ORM\Column(type="string")
     */
    private string $secondPartyNote;

    /**
     * @var Collection|TransactionCategory[]
     * @ORM\ManyToOne(targetEntity="TransactionCategory", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var Collection|Ledger[]
     * @ORM\ManyToOne(targetEntity="Ledger", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ledger;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a string representation of this entity.
     */
    public function __toString() : string {
        return $this->firstParty . ' ' . $this->conjunction . ' ' . $this->secondParty;
    }

    public function getDate() : ?DateTimeInterface {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date) : self {
        $this->date = $date;

        return $this;
    }

    public function getPage() : ?int {
        return $this->page;
    }

    public function setPage(int $page) : self {
        $this->page = $page;

        return $this;
    }

    public function getNotes() : ?string {
        return $this->notes;
    }

    public function setNotes(?string $notes) : self {
        $this->notes = $notes;

        return $this;
    }

    public function getFirstPartyNote() : ?string {
        return $this->firstPartyNote;
    }

    public function setFirstPartyNote(string $firstPartyNote) : self {
        $this->firstPartyNote = $firstPartyNote;

        return $this;
    }

    public function getConjunction() : ?string {
        return $this->conjunction;
    }

    public function setConjunction(string $conjunction) : self {
        $this->conjunction = $conjunction;

        return $this;
    }

    public function getSecondPartyNote() : ?string {
        return $this->secondPartyNote;
    }

    public function setSecondPartyNote(string $secondPartyNote) : self {
        $this->secondPartyNote = $secondPartyNote;

        return $this;
    }

    public function getFirstParty() : ?Person {
        return $this->firstParty;
    }

    public function setFirstParty(?Person $firstParty) : self {
        $this->firstParty = $firstParty;

        return $this;
    }

    public function getSecondParty() : ?Person {
        return $this->secondParty;
    }

    public function setSecondParty(?Person $secondParty) : self {
        $this->secondParty = $secondParty;

        return $this;
    }

    public function getCategory() : ?TransactionCategory {
        return $this->category;
    }

    public function setCategory(?TransactionCategory $category) : self {
        $this->category = $category;

        return $this;
    }

    public function getLedger() : ?Ledger {
        return $this->ledger;
    }

    public function setLedger(?Ledger $ledger) : self {
        $this->ledger = $ledger;

        return $this;
    }
}
