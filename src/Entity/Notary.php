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
 * Notary.
 *
 * @ORM\Table(name="notary")
 * @ORM\Entity(repositoryClass="App\Repository\NotaryRepository")
 */
class Notary extends AbstractEntity {
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

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
     */
    public function __toString() : string {
        return $this->name;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function setName(string $name) : self {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Ledger[]
     */
    public function getLedgers() : Collection {
        return $this->ledgers;
    }

    public function addLedger(Ledger $ledger) : self {
        if ( ! $this->ledgers->contains($ledger)) {
            $this->ledgers[] = $ledger;
            $ledger->setNotary($this);
        }

        return $this;
    }

    public function removeLedger(Ledger $ledger) : self {
        if ($this->ledgers->removeElement($ledger)) {
            // set the owning side to null (unless already changed)
            if ($ledger->getNotary() === $this) {
                $ledger->setNotary(null);
            }
        }

        return $this;
    }
}
