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
}
