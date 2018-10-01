<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Witness
 *
 * @ORM\Table(name="witness")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WitnessRepository")
 */
class Witness extends AbstractEntity {

    public function __toString() {

    }

}
