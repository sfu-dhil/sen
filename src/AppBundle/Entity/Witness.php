<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Witness
 *
 * @ORM\Table(name="witness")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WitnessRepository")
 */
class Witness extends AbstractEntity
{

    /**
     * @var Collection|WitnessCategory[]
     * @ORM\ManyToOne(targetEntity="WitnessCategory", inversedBy="witnesses")
     */
    private $category;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="witnesses")
     */
    private $person;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="witnesses")
     */
    private $event;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }
}
