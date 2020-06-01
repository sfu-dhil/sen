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
 * Notary.
 *
 * @ORM\Table(name="notary")
 * @ORM\Entity(repositoryClass="App\Repository\NotaryRepository")
 */
class Notary extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

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
    public function __toString() : string {
        return $this->name;
    }

    /**
     * Add ledger.
     *
     * @return Notary
     */
    public function addLedger(Ledger $ledger) {
        $this->ledgers[] = $ledger;

        return $this;
    }

    /**
     * Remove ledger.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLedger(Ledger $ledger) {
        return $this->ledgers->removeElement($ledger);
    }

    /**
     * Get ledgers.
     *
     * @return Collection
     */
    public function getLedgers() {
        return $this->ledgers;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Notary
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }
}
