<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionRepository")
 */
class Transaction extends AbstractEntity
{

    /**
     * @var Collection|TransactionCategory[]
     * @ORM\ManyToOne(targetEntity="TransactionCategory", inversedBy="transactions")
     */
    private $category;

    /**
     * @var Collection|Ledger[]
     * @ORM\ManyToOne(targetEntity="Ledger", inversedBy="transactions")
     */
    private $ledger;

    /**
     * @var Collection|People
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="transactions")
     */
    private $people;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }
}
