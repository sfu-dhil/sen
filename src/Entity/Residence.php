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
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $address;

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

    public function getDate() : ?string {
        return $this->date;
    }

    public function setDate(?string $date) : self {
        $this->date = $date;

        return $this;
    }

    public function getPerson() : ?Person {
        return $this->person;
    }

    public function setPerson(?Person $person) : self {
        $this->person = $person;

        return $this;
    }

    public function getCity() : ?City {
        return $this->city;
    }

    public function setCity(?City $city) : self {
        $this->city = $city;

        return $this;
    }
}
