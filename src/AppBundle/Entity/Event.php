<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var EventCategory
     * @ORM\ManyToOne(targetEntity="EventCategory", inversedBy="events")
     */
    private $category;

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

    public function __construct() {
        parent::__construct();
        $this->participants = new ArrayCollection();
        $this->witnesses = new ArrayCollection();
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
     * @param EventCategory|null $category
     *
     * @return Event
     */
    public function setCategory(EventCategory $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return EventCategory|null
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Add participant.
     *
     * @param Person $participant
     *
     * @return Event
     */
    public function addParticipant(Person $participant) {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove participant.
     *
     * @param Person $participant
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeParticipant(Person $participant) {
        return $this->participants->removeElement($participant);
    }

    /**
     * Get participants.
     *
     * @return Collection
     */
    public function getParticipants() {
        return $this->participants;
    }

    /**
     * Add witness.
     *
     * @param Witness $witness
     *
     * @return Event
     */
    public function addWitness(Witness $witness) {
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

    /**
     * Set location.
     *
     * @param Location|null $location
     *
     * @return Event
     */
    public function setLocation(Location $location = null) {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return Location|null
     */
    public function getLocation() {
        return $this->location;
    }
}
