<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
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
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $writtenDate = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $date = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $note = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $recordSource = null;

    /**
     * @ORM\ManyToOne(targetEntity="EventCategory", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private EventCategory $category;

    /**
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="events")
     */
    private $participants;

    /**
     * @var Collection|Witness[]
     * @ORM\OneToMany(targetEntity="Witness", mappedBy="event", cascade={"remove"})
     */
    private $witnesses;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="events")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Location $location = null;

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

    public function getWrittenDate() : ?string {
        return $this->writtenDate;
    }

    public function setWrittenDate(?string $writtenDate) : self {
        $this->writtenDate = $writtenDate;

        return $this;
    }

    public function getDate() : ?string {
        return $this->date;
    }

    public function setDate(?string $date) : self {
        $this->date = $date;

        return $this;
    }

    public function getNote() : ?string {
        return $this->note;
    }

    public function setNote(?string $note) : self {
        $this->note = $note;

        return $this;
    }

    public function getCategory() : ?EventCategory {
        return $this->category;
    }

    public function setCategory(?EventCategory $category) : self {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getParticipants() : Collection {
        return $this->participants;
    }

    public function addParticipant(Person $participant) : self {
        if ( ! $this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Person $participant) : self {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return Collection|Witness[]
     */
    public function getWitnesses() : Collection {
        return $this->witnesses;
    }

    public function addWitness(Witness $witness) : self {
        if ( ! $this->witnesses->contains($witness)) {
            $this->witnesses[] = $witness;
            $witness->setEvent($this);
        }

        return $this;
    }

    public function removeWitness(Witness $witness) : self {
        if ($this->witnesses->removeElement($witness)) {
            // set the owning side to null (unless already changed)
            if ($witness->getEvent() === $this) {
                $witness->setEvent(null);
            }
        }

        return $this;
    }

    public function getLocation() : ?Location {
        return $this->location;
    }

    public function setLocation(?Location $location) : self {
        $this->location = $location;

        return $this;
    }

    public function getRecordSource() : ?string {
        return $this->recordSource;
    }

    public function setRecordSource(?string $recordSource) : self {
        $this->recordSource = $recordSource;

        return $this;
    }
}
