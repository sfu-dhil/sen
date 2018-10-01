<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * LocationCategory
 *
 * @ORM\Table(name="location_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocationCategoryRepository")
 */
class LocationCategory extends AbstractTerm
{

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
     * @param Location $location
     *
     * @return LocationCategory
     */
    public function addLocation(Location $location)
    {
        $this->locations[] = $location;

        return $this;
    }

    /**
     * Remove location.
     *
     * @param Location $location
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLocation(Location $location)
    {
        return $this->locations->removeElement($location);
    }

    /**
     * Get locations.
     *
     * @return Collection
     */
    public function getLocations()
    {
        return $this->locations;
    }
}
