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
 * LocationCategory.
 *
 * @ORM\Table(name="location_category")
 * @ORM\Entity(repositoryClass="App\Repository\LocationCategoryRepository")
 */
class LocationCategory extends AbstractTerm {
    /**
     * @var Collection|Location[]
     * @ORM\OneToMany(targetEntity="Location", mappedBy="category")
     */
    private $locations;

    public function __construct() {
        parent::__construct();
        $this->locations = new ArrayCollection();
    }

    /**
     * @return Collection|Location[]
     */
    public function getLocations() : Collection {
        return $this->locations;
    }

    public function addLocation(Location $location) : self {
        if ( ! $this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setCategory($this);
        }

        return $this;
    }

    public function removeLocation(Location $location) : self {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getCategory() === $this) {
                $location->setCategory(null);
            }
        }

        return $this;
    }
}
