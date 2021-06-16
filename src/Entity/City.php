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
 * City.
 *
 * @ORM\Table(name="city", indexes={
 *     @ORM\Index(name="city_ft_idx", columns={"name"}, flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 */
class City extends AbstractEntity {
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

    /**
     * @var Collection|Residence[]
     * @ORM\OneToMany(targetEntity="Residence", mappedBy="city")
     */
    private $residences;

    public function __construct() {
        parent::__construct();
        $this->residences = new ArrayCollection();
    }

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

    /**
     * @return Collection|Residence[]
     */
    public function getResidences() : Collection {
        return $this->residences;
    }

    public function addResidence(Residence $residence) : self {
        if ( ! $this->residences->contains($residence)) {
            $this->residences[] = $residence;
            $residence->setCity($this);
        }

        return $this;
    }

    public function removeResidence(Residence $residence) : self {
        if ($this->residences->removeElement($residence)) {
            // set the owning side to null (unless already changed)
            if ($residence->getCity() === $this) {
                $residence->setCity(null);
            }
        }

        return $this;
    }
}
