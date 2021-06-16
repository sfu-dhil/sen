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
 * Residence.
 *
 * @ORM\Table(name="residence")
 * @ORM\Entity(repositoryClass="App\Repository\ResidenceRepository")
 */
class Residence extends AbstractEntity {
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $date = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $address = null;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="residences")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $person;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="residences")
     * @ORM\JoinColumn(nullable=false)
     */
    private City $city;

    public function __toString() : string {
        return $this->date . ' ' . $this->city;
    }

    public function getDate() : ?string {
        return $this->date;
    }

    public function setDate(string $date) : self {
        $this->date = $date;

        return $this;
    }

    public function getPerson() : ?Person {
        return $this->person;
    }

    public function setPerson(Person $person) : self {
        $this->person = $person;

        return $this;
    }

    public function getCity() : ?City {
        return $this->city;
    }

    public function setCity(City $city) : self {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress() : ?string {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(?string $address) : void {
        $this->address = $address;
    }
}
