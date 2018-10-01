<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event extends AbstractEntity
{

    /**
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="events")
     */
    private $participants;

    /**
     * @var Collection|Witness[]
     * @ORM\OneToMany(targetEntity="Witness", mappedBy="event")
     */
    private $witnesses;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="events")
     */
    private $location;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }
}
