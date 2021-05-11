<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
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
class Transaction extends AbstractEntity {
    /**
     * @var DateTimeImmutable
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

}
