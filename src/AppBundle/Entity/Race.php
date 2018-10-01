<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Race
 *
 * @ORM\Table(name="race")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceRepository")
 */
class Race extends \Nines\UtilBundle\Entity\AbstractTerm
{
    /**
     * @var Collection|Person[]
     * @ORM\OneToMany(targetEntity="Person", mappedBy="race")
     */
    private $people;
}
