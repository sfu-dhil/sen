<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Notary
 *
 * @ORM\Table(name="notary")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotaryRepository")
 */
class Notary extends AbstractEntity {

    public function __toString() {

    }

}
