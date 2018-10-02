<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Residence
 *
 * @ORM\Table(name="residence")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResidenceRepository")
 */
class Residence extends AbstractEntity
{

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $date;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="residences")
     */
    private $person;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="City")
     */
    private $city;

    public function __toString() {
        return "";
    }


    /**
     * Set date.
     *
     * @param string $date
     *
     * @return Residence
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set person.
     *
     * @param \AppBundle\Entity\Person|null $person
     *
     * @return Residence
     */
    public function setPerson(\AppBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return \AppBundle\Entity\Person|null
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set city.
     *
     * @param \AppBundle\Entity\City|null $city
     *
     * @return Residence
     */
    public function setCity(\AppBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return \AppBundle\Entity\City|null
     */
    public function getCity()
    {
        return $this->city;
    }
}
