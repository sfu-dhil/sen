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
     */
    public function __toString() : string {
        return $this->notary . ' ' . $this->volume;
    }
}
