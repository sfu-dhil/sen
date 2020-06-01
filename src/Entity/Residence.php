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
 * Residence.
 *
 * @ORM\Table(name="residence")
 * @ORM\Entity(repositoryClass="App\Repository\ResidenceRepository")
 */
class Residence extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $date;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="residences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    public function __toString() : string {
        return $this->date . ' ' . $this->city;
    }

    /**
     * Set date.
     *
     * @param string $date
     *
     * @return Residence
     */
    public function setDate($date) {
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

    /**
     * Set person.
     *
     * @param null|\App\Entity\Person $person
     *
     * @return Residence
     */
    public function setPerson(Person $person = null) {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return null|\App\Entity\Person
     */
    public function getPerson() {
        return $this->person;
    }

    /**
     * Set city.
     *
     * @param null|\App\Entity\City $city
     *
     * @return Residence
     */
    public function setCity(City $city = null) {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return null|\App\Entity\City
     */
    public function getCity() {
        return $this->city;
    }
}
