<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 */
class Person extends AbstractEntity
{

    /**
     * @var Race
     * @ORM\ManyToOne(targetEntity="Race", inversedBy="people")
     */
    private $race;

    /**
     * @var Collection|Transaction[]
     * @ORM\ManyToMany(targetEntity="Transaction", mappedBy="people")
     */
    private $transactions;

    /**
     * @var Collection|Relationship[]
     * @ORM\ManyToMany(targetEntity="Relationship", inversedBy="people")
     */
    private $relationships;

    /**
     * @var Collection|Witness[]
     * @ORM\OneToMany(targetEntity="Witness", mappedBy="person")
     */
    private $witnesses;

    /**
     * @var Collection|Event[]
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="participants")
     */
    private $events;

    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return get_class($this) . "#" . $this->getId();
    }

    /**
     * Set race.
     *
     * @param Race|null $race
     *
     * @return Person
     */
    public function setRace(Race $race = null)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get race.
     *
     * @return Race|null
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * Add transaction.
     *
     * @param Transaction $transaction
     *
     * @return Person
     */
    public function addTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction.
     *
     * @param Transaction $transaction
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTransaction(Transaction $transaction)
    {
        return $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions.
     *
     * @return Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Add relationship.
     *
     * @param Relationship $relationship
     *
     * @return Person
     */
    public function addRelationship(Relationship $relationship)
    {
        $this->relationships[] = $relationship;

        return $this;
    }

    /**
     * Remove relationship.
     *
     * @param Relationship $relationship
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRelationship(Relationship $relationship)
    {
        return $this->relationships->removeElement($relationship);
    }

    /**
     * Get relationships.
     *
     * @return Collection
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Add witness.
     *
     * @param Witness $witness
     *
     * @return Person
     */
    public function addWitness(Witness $witness)
    {
        $this->witnesses[] = $witness;

        return $this;
    }

    /**
     * Remove witness.
     *
     * @param Witness $witness
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeWitness(Witness $witness)
    {
        return $this->witnesses->removeElement($witness);
    }

    /**
     * Get witnesses.
     *
     * @return Collection
     */
    public function getWitnesses()
    {
        return $this->witnesses;
    }

    /**
     * Add event.
     *
     * @param Event $event
     *
     * @return Person
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event.
     *
     * @param Event $event
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEvent(Event $event)
    {
        return $this->events->removeElement($event);
    }

    /**
     * Get events.
     *
     * @return Collection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
