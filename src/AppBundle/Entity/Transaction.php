<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
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
     * @var Collection|Person[]
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

    /**
     * Set category.
     *
     * @param TransactionCategory|null $category
     *
     * @return Transaction
     */
    public function setCategory(TransactionCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return TransactionCategory|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set ledger.
     *
     * @param Ledger|null $ledger
     *
     * @return Transaction
     */
    public function setLedger(Ledger $ledger = null)
    {
        $this->ledger = $ledger;

        return $this;
    }

    /**
     * Get ledger.
     *
     * @return Ledger|null
     */
    public function getLedger()
    {
        return $this->ledger;
    }

    /**
     * Add person.
     *
     * @param Person $person
     *
     * @return Transaction
     */
    public function addPerson(Person $person)
    {
        $this->people[] = $person;

        return $this;
    }

    /**
     * Remove person.
     *
     * @param Person $person
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePerson(Person $person)
    {
        return $this->people->removeElement($person);
    }

    /**
     * Get people.
     *
     * @return Collection
     */
    public function getPeople()
    {
        return $this->people;
    }
}
