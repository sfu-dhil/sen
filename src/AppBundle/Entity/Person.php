<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    const MALE = 'M';

    const FEMALE = 'F';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $alias;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $native;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $occupation;

    /**
     * One of M or F.
     *
     * @var string
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $sex;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $birthDate;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="City")
     */
    private $birthPlace;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $birthStatus;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var Collection|Residence[]
     * @ORM\OneToMany(targetEntity="Residence", mappedBy="person")
     */
    private $residences;

    /**
     * @var Race
     * @ORM\ManyToOne(targetEntity="Race", inversedBy="people")
     */
    private $race;

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
     * @var Collection|Transaction[]
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="firstParty")
     */
    private $firstPartyTransactions;

    /**
     * @var Collection|Transaction[]
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="secondParty")
     */
    private $secondPartyTransactions;

    public function __construct() {
        parent::__construct();
        $this->alias = array();
        $this->transactions = new ArrayCollection();
        $this->relationships = new ArrayCollection();
        $this->witnesses = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->residences = new ArrayCollection();
        $this->firstPartyTransactions = new ArrayCollection();
        $this->secondPartyTransactions = new ArrayCollection();
    }


    /**
     * Returns a string representation of this entity.
     *
     * @return string
     */
    public function __toString() {
        return $this->lastName . ", " . $this->firstName;
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

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return Person
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set alias.
     *
     * @param array $alias
     *
     * @return Person
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias.
     *
     * @return array
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set native.
     *
     * @param string $native
     *
     * @return Person
     */
    public function setNative($native)
    {
        $this->native = $native;

        return $this;
    }

    /**
     * Get native.
     *
     * @return string
     */
    public function getNative()
    {
        return $this->native;
    }

    /**
     * Set occupation.
     *
     * @param string $occupation
     *
     * @return Person
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation.
     *
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set sex.
     *
     * @param string|null $sex
     *
     * @return Person
     */
    public function setSex($sex = null)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex.
     *
     * @return string|null
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set birthDate.
     *
     * @param string $birthDate
     *
     * @return Person
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate.
     *
     * @return string
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birthStatus.
     *
     * @param string $birthStatus
     *
     * @return Person
     */
    public function setBirthStatus($birthStatus)
    {
        $this->birthStatus = $birthStatus;

        return $this;
    }

    /**
     * Get birthStatus.
     *
     * @return string
     */
    public function getBirthStatus()
    {
        return $this->birthStatus;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Person
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set birthPlace.
     *
     * @param City|null $birthPlace
     *
     * @return Person
     */
    public function setBirthPlace(City $birthPlace = null)
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get birthPlace.
     *
     * @return City|null
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * Add residence.
     *
     * @param Residence $residence
     *
     * @return Person
     */
    public function addResidence(Residence $residence)
    {
        $this->residences[] = $residence;

        return $this;
    }

    /**
     * Remove residence.
     *
     * @param Residence $residence
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeResidence(Residence $residence)
    {
        return $this->residences->removeElement($residence);
    }

    /**
     * Get residences.
     *
     * @return Collection
     */
    public function getResidences()
    {
        return $this->residences;
    }

    /**
     * Add firstPartyTransaction.
     *
     * @param Transaction $firstPartyTransaction
     *
     * @return Person
     */
    public function addFirstPartyTransaction(Transaction $firstPartyTransaction)
    {
        $this->firstPartyTransactions[] = $firstPartyTransaction;

        return $this;
    }

    /**
     * Remove firstPartyTransaction.
     *
     * @param Transaction $firstPartyTransaction
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFirstPartyTransaction(Transaction $firstPartyTransaction)
    {
        return $this->firstPartyTransactions->removeElement($firstPartyTransaction);
    }

    /**
     * Get firstPartyTransactions.
     *
     * @return Collection
     */
    public function getFirstPartyTransactions()
    {
        return $this->firstPartyTransactions;
    }

    /**
     * Add secondPartyTransaction.
     *
     * @param Transaction $secondPartyTransaction
     *
     * @return Person
     */
    public function addSecondPartyTransaction(Transaction $secondPartyTransaction)
    {
        $this->secondPartyTransactions[] = $secondPartyTransaction;

        return $this;
    }

    /**
     * Remove secondPartyTransaction.
     *
     * @param Transaction $secondPartyTransaction
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSecondPartyTransaction(Transaction $secondPartyTransaction)
    {
        return $this->secondPartyTransactions->removeElement($secondPartyTransaction);
    }

    /**
     * Get secondPartyTransactions.
     *
     * @return Collection
     */
    public function getSecondPartyTransactions()
    {
        return $this->secondPartyTransactions;
    }
}
