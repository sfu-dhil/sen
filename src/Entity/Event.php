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
 * Event.
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $writtenDate;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $note;

    /**
     * @var EventCategory
     * @ORM\ManyToOne(targetEntity="EventCategory", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
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
     * @ORM\JoinColumn(nullable=true)
     */
    private $location;

    public function __construct() {
        parent::__construct();
        $this->participants = new ArrayCollection();
        $this->witnesses = new ArrayCollection();
    }

    /**
     * Returns a string representation of this entity.
     */
    public function __toString() : string {
        return $this->category . ' ' . $this->date;
    }

    public function setNote($note) : void {
        $this->note = $note;
    }

    public function getNote() {
        return $this->note;
    }

    /**
     * Set category.
     *
     * @return Event
     */
    public function setCategory(?EventCategory $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return null|EventCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Add participant.
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
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
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
     * @return Event
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

    /**
     * Set location.
     *
     * @return Event
     */
    public function setLocation(?Location $location = null) {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return null|Location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Set writtenDate.
     *
     * @param null|string $writtenDate
     *
     * @return Event
     */
    public function setWrittenDate($writtenDate = null) {
        $this->writtenDate = $writtenDate;

        return $this;
    }

    /**
     * Get writtenDate.
     *
     * @return null|string
     */
    public function getWrittenDate() {
        return $this->writtenDate;
    }

    /**
     * Set date.
     *
     * @param string $date
     *
     * @return Event
     */
    public function setDate($date = null) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate() {
        return $this->date;
    }
}
