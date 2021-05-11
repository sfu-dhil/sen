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
     * Add location.
     *
     * @return LocationCategory
     */
    public function addLocation(Location $location) {
        $this->locations[] = $location;

        return $this;
    }

    /**
     * Remove location.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLocation(Location $location) {
        return $this->locations->removeElement($location);
    }

    /**
     * Get locations.
     *
     * @return Collection
     */
    public function getLocations() {
        return $this->locations;
    }
}
