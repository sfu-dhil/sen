<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Ledger
 *
 * @ORM\Table(name="ledger")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LedgerRepository")
 */
class Ledger extends AbstractEntity {

    public function __toString() {

    }

}
