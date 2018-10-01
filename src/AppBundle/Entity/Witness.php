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
     * @var WitnessCategory
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

    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }

    /**
     * Set category.
     *
     * @param WitnessCategory|null $category
     *
     * @return Witness
     */
    public function setCategory(WitnessCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return WitnessCategory|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set person.
     *
     * @param Person|null $person
     *
     * @return Witness
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return Person|null
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set event.
     *
     * @param Event|null $event
     *
     * @return Witness
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event.
     *
     * @return Event|null
     */
    public function getEvent()
    {
        return $this->event;
    }
}
