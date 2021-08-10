<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Witness.
 *
 * @ORM\Table(name="witness")
 * @ORM\Entity(repositoryClass="App\Repository\WitnessRepository")
 */
class Witness extends AbstractEntity {
    /**
     * @ORM\ManyToOne(targetEntity="WitnessCategory", inversedBy="witnesses")
     */
    private WitnessCategory $category;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="witnesses")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $person;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="witnesses")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Event $event;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a string representation of this entity.
     */
    public function __toString() : string {
        return $this->person . ' ' . $this->event . ' ' . $this->category;
    }

    public function getCategory() : ?WitnessCategory {
        return $this->category;
    }

    public function setCategory(?WitnessCategory $category) : self {
        $this->category = $category;

        return $this;
    }

    public function getPerson() : ?Person {
        return $this->person;
    }

    public function setPerson(?Person $person) : self {
        $this->person = $person;

        return $this;
    }

    public function getEvent() : ?Event {
        return $this->event;
    }

    public function setEvent(?Event $event) : self {
        $this->event = $event;

        return $this;
    }
}
