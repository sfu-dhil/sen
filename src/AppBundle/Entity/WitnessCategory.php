<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WitnessCategory
 *
 * @ORM\Table(name="witness_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WitnessCategoryRepository")
 */
class WitnessCategory extends \Nines\UtilBundle\Entity\AbstractTerm
{

    /**
     * @var Collection|Witness[]
     * @ORM\OneToMany(targetEntity="Witness", mappedBy="category")
     */
    private $witnesses;

}
