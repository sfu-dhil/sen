<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * WitnessCategory
 *
 * @ORM\Table(name="witness_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WitnessCategoryRepository")
 */
class WitnessCategory extends AbstractTerm
{

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
     * @param Witness $witness
     *
     * @return WitnessCategory
     */
    public function addWitness(Witness $witness)
    {
        $this->witnesses[] = $witness;

        return $this;
    }

    /**
     * Remove witness.
     *
     * @param Witness $witness
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeWitness(Witness $witness)
    {
        return $this->witnesses->removeElement($witness);
    }

    /**
     * Get witnesses.
     *
     * @return Collection
     */
    public function getWitnesses()
    {
        return $this->witnesses;
    }
}
