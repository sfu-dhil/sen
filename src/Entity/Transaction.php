<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Transaction.
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction extends AbstractEntity
{
    /**
     * @var DateTime
     * @ORM\Column(type="date", nullable=false)
     */
    private $date;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $page;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $notes;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="firstPartyTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $firstParty;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $firstPartyNote;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $conjunction;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="secondPartyTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $secondParty;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $secondPartyNote;

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

    /**
     * Set category.
     *
     * @param ?TransactionCategory $category
     *
     * @return Transaction
     */
    public function setCategory(?TransactionCategory $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return null|TransactionCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set ledger.
     *
     * @param ?Ledger $ledger
     *
     * @return Transaction
     */
    public function setLedger(?Ledger $ledger = null) {
        $this->ledger = $ledger;

        return $this;
    }

    /**
     * Get ledger.
     *
     * @return null|Ledger
     */
    public function getLedger() {
        return $this->ledger;
    }

    /**
     * Set firstPartyNote.
     *
     * @param string $firstPartyNote
     *
     * @return Transaction
     */
    public function setFirstPartyNote($firstPartyNote) {
        $this->firstPartyNote = $firstPartyNote;

        return $this;
    }

    /**
     * Get firstPartyNote.
     *
     * @return string
     */
    public function getFirstPartyNote() {
        return $this->firstPartyNote;
    }

    /**
     * Set conjunction.
     *
     * @param string $conjunction
     *
     * @return Transaction
     */
    public function setConjunction($conjunction) {
        $this->conjunction = $conjunction;

        return $this;
    }

    /**
     * Get conjunction.
     *
     * @return string
     */
    public function getConjunction() {
        return $this->conjunction;
    }

    /**
     * Set secondPartyNote.
     *
     * @param string $secondPartyNote
     *
     * @return Transaction
     */
    public function setSecondPartyNote($secondPartyNote) {
        $this->secondPartyNote = $secondPartyNote;

        return $this;
    }

    /**
     * Get secondPartyNote.
     *
     * @return string
     */
    public function getSecondPartyNote() {
        return $this->secondPartyNote;
    }

    /**
     * Set firstParty.
     *
     * @param ?Person $firstParty
     *
     * @return Transaction
     */
    public function setFirstParty(?Person $firstParty = null) {
        $this->firstParty = $firstParty;

        return $this;
    }

    /**
     * Get firstParty.
     *
     * @return null|Person
     */
    public function getFirstParty() {
        return $this->firstParty;
    }

    /**
     * Set secondParty.
     *
     * @param ?Person $secondParty
     *
     * @return Transaction
     */
    public function setSecondParty(?Person $secondParty = null) {
        $this->secondParty = $secondParty;

        return $this;
    }

    /**
     * Get secondParty.
     *
     * @return null|Person
     */
    public function getSecondParty() {
        return $this->secondParty;
    }

    /**
     * Set date.
     *
     * @param DateTime $date
     *
     * @return Transaction
     */
    public function setDate(DateTimeImmutable $date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set page.
     *
     * @param int $page
     *
     * @return Transaction
     */
    public function setPage($page) {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page.
     *
     * @return int
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * Set notes.
     *
     * @param string $notes
     *
     * @return Transaction
     */
    public function setNotes($notes) {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return string
     */
    public function getNotes() {
        return $this->notes;
    }
}
