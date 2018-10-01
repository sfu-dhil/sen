<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 */
class Person extends AbstractEntity
{

    /**
     * @var Collection|Transaction[]
     * @ORM\ManyToMany(targetEntity="Transaction", mappedBy="people")
     */
    private $transactions;

    /**
     * @var Collection|Relationship[]
     * @ORM\ManyToMany(targetEntity="Relationship", inversedBy="people")
     */
    private $relationships;

    /**
     * @var Collection|Witness[]
     * @ORM\OneToMany(targetEntity="Witness", mappedBy="person")
     */
    private $witnesses;

    /**
     * @var Collection|Event[]
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="participants")
     */
    private $events;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }
}
