<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LocationCategory
 *
 * @ORM\Table(name="location_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocationCategoryRepository")
 */
class LocationCategory extends \Nines\UtilBundle\Entity\AbstractTerm
{
    /**
     * @var Collection|Location[]
     * @ORM\OneToMany(targetEntity="Location", mappedBy="category")
     */
    private $locations;
    
}
