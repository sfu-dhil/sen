<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
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
     * @var WitnessCategory
     * @ORM\ManyToOne(targetEntity="WitnessCategory", inversedBy="witnesses")
     */
    private $category;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="witnesses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="witnesses")
     * @ORM\JoinColumn(nullable=false)
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
    public function __toString() : string {
        return $this->person . ' ' . $this->event . ' ' . $this->category;
    }

    /**
     * Set category.
     *
     * @return Witness
     */
    public function setCategory(WitnessCategory $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return null|WitnessCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set person.
     *
     * @return Witness
     */
    public function setPerson(Person $person = null) {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return null|Person
     */
    public function getPerson() {
        return $this->person;
    }

    /**
     * Set event.
     *
     * @return Witness
     */
    public function setEvent(Event $event = null) {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event.
     *
     * @return null|Event
     */
    public function getEvent() {
        return $this->event;
    }
}
