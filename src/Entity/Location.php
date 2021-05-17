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
 * Location.
 *
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var LocationCategory
     * @ORM\ManyToOne(targetEntity="LocationCategory", inversedBy="locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var Collection|Event[]
     * @ORM\OneToMany(targetEntity="Event", mappedBy="location")
     */
    private $events;

    public function __construct() {
        parent::__construct();
        $this->events = new ArrayCollection();
    }

    /**
     * Returns a string representation of this entity.
     */
    public function __toString() : string {
        return $this->name;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function setName(string $name) : self {
        $this->name = $name;

        return $this;
    }

    public function getCategory() : ?LocationCategory {
        return $this->category;
    }

    public function setCategory(?LocationCategory $category) : self {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents() : Collection {
        return $this->events;
    }

    public function addEvent(Event $event) : self {
        if ( ! $this->events->contains($event)) {
            $this->events[] = $event;
            $event->setLocation($this);
        }

        return $this;
    }

    public function removeEvent(Event $event) : self {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getLocation() === $this) {
                $event->setLocation(null);
            }
        }

        return $this;
    }
}
