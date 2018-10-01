<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelationshipCategory
 *
 * @ORM\Table(name="relationship_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RelationshipCategoryRepository")
 */
class RelationshipCategory extends \Nines\UtilBundle\Entity\AbstractTerm
{

    /**
     * @var Collection|Relationship[]
     * @ORM\OneToMany(targetEntity="Relationship", mappedBy="category")
     */
    private $relationships;

}
