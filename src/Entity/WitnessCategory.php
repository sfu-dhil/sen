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
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * WitnessCategory.
 *
 * @ORM\Table(name="witness_category")
 * @ORM\Entity(repositoryClass="App\Repository\WitnessCategoryRepository")
 */
class WitnessCategory extends AbstractTerm {
    /**
     * @var Collection|Witness[]
     * @ORM\OneToMany(targetEntity="Witness", mappedBy="category")
     */
    private $witnesses;

    public function __construct() {
        parent::__construct();
        $this->witnesses = new ArrayCollection();
    }

    /**
     * Add witness.
     *
     * @return WitnessCategory
     */
    public function addWitness(Witness $witness) {
        $this->witnesses[] = $witness;

        return $this;
    }

    /**
     * Remove witness.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeWitness(Witness $witness) {
        return $this->witnesses->removeElement($witness);
    }

    /**
     * Get witnesses.
     *
     * @return Collection
     */
    public function getWitnesses() {
        return $this->witnesses;
    }
}
