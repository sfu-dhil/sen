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
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * EventCategory.
 *
 * @ORM\Table(name="event_category")
 * @ORM\Entity(repositoryClass="App\Repository\EventCategoryRepository")
 */
class EventCategory extends AbstractTerm {
    /**
     * @var Collection|Event[]
     * @ORM\OneToMany(targetEntity="Event", mappedBy="category")
     */
    private $events;

    public function __construct() {
        parent::__construct();
        $this->events = new ArrayCollection();
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
            $event->setCategory($this);
        }

        return $this;
    }

    public function removeEvent(Event $event) : self {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCategory() === $this) {
                $event->setCategory(null);
            }
        }

        return $this;
    }
}
