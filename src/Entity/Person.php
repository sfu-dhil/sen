<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Person.
 *
 * @ORM\Table(name="person",
 *     indexes={
 *         @ORM\Index(name="person_ft_idx", columns={"first_name", "last_name"}, flags={"fulltext"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person extends AbstractEntity {
    public const MALE = 'M';

    public const FEMALE = 'F';

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
     * @var string
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $title;

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
     * @var array
     * @ORM\Column(type="array", nullable=true)
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $birthStatus;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\OneToMany(targetEntity="Relationship", mappedBy="person")
     */
    private $relationships;

    /**
     * @var Collection|Relationship[]
     * @ORM\OneToMany(targetEntity="Relationship", mappedBy="relation")
     */
    private $relations;

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
        $this->alias = [];
        $this->occupation = [];
        $this->transactions = new ArrayCollection();
        $this->relationships = new ArrayCollection();
        $this->witnesses = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->residences = new ArrayCollection();
        $this->firstPartyTransactions = new ArrayCollection();
        $this->secondPartyTransactions = new ArrayCollection();
        $this->relations = new ArrayCollection();
    }

    /**
     * Returns a string representation of this entity.
     */
    public function __toString() : string {
        return ($this->lastName ?: '?') . ', ' . ($this->firstName ?: '?');
    }

    public function getFirstName() : ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName) : self {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName() : ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName) : self {
        $this->lastName = mb_convert_case($lastName, MB_CASE_TITLE);

        return $this;
    }

    public function getTitle() : ?string {
        return $this->title;
    }

    public function setTitle(string $title) : self {
        $this->title = $title;

        return $this;
    }

    public function getAlias() : ?array {
        return $this->alias;
    }

    public function setAlias(array $alias) : self {
        $this->alias = $alias;

        return $this;
    }

    public function addAlias($alias) : self {
        if (is_string($alias)) {
            $this->alias[] = $alias;
        }
        if (is_array($alias)) {
            $this->alias = array_merge($this->alias, $alias);
        }

        return $this;
    }

    public function getNative() : ?string {
        return $this->native;
    }

    public function setNative(?string $native) : self {
        $this->native = $native;

        return $this;
    }

    public function getOccupation() : ?array {
        return $this->occupation;
    }

    public function setOccupation(?array $occupation) : self {
        $this->occupation = $occupation;

        return $this;
    }

    public function addOccupation($occupation) : self {
        if (is_string($occupation)) {
            $this->occupation[] = $occupation;
        }
        if (is_array($occupation)) {
            $this->occupation = array_merge($this->occupation, $occupation);
        }

        return $this;
    }

    public function getSex() : ?string {
        return $this->sex;
    }

    public function setSex(?string $sex) : self {
        $this->sex = $sex;

        return $this;
    }

    public function getBirthStatus() : ?string {
        return $this->birthStatus;
    }

    public function setBirthStatus(?string $birthStatus) : self {
        $this->birthStatus = $birthStatus;

        return $this;
    }

    public function getStatus() : ?string {
        return $this->status;
    }

    public function setStatus(?string $status) : self {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Residence[]
     */
    public function getResidences() : Collection {
        return $this->residences;
    }

    public function addResidence(Residence $residence) : self {
        if ( ! $this->residences->contains($residence)) {
            $this->residences[] = $residence;
            $residence->setPerson($this);
        }

        return $this;
    }

    public function removeResidence(Residence $residence) : self {
        if ($this->residences->removeElement($residence)) {
            // set the owning side to null (unless already changed)
            if ($residence->getPerson() === $this) {
                $residence->setPerson(null);
            }
        }

        return $this;
    }

    public function getRace() : ?Race {
        return $this->race;
    }

    public function setRace(?Race $race) : self {
        $this->race = $race;

        return $this;
    }

    /**
     * @return Collection|Relationship[]
     */
    public function getRelationships() : Collection {
        return $this->relationships;
    }

    public function addRelationship(Relationship $relationship) : self {
        if ( ! $this->relationships->contains($relationship)) {
            $this->relationships[] = $relationship;
            $relationship->setPerson($this);
        }

        return $this;
    }

    public function removeRelationship(Relationship $relationship) : self {
        if ($this->relationships->removeElement($relationship)) {
            // set the owning side to null (unless already changed)
            if ($relationship->getPerson() === $this) {
                $relationship->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Relationship[]
     */
    public function getRelations() : Collection {
        return $this->relations;
    }

    public function addRelation(Relationship $relation) : self {
        if ( ! $this->relations->contains($relation)) {
            $this->relations[] = $relation;
            $relation->setRelation($this);
        }

        return $this;
    }

    public function removeRelation(Relationship $relation) : self {
        if ($this->relations->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getRelation() === $this) {
                $relation->setRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Witness[]
     */
    public function getWitnesses() : Collection {
        return $this->witnesses;
    }

    public function addWitness(Witness $witness) : self {
        if ( ! $this->witnesses->contains($witness)) {
            $this->witnesses[] = $witness;
            $witness->setPerson($this);
        }

        return $this;
    }

    public function removeWitness(Witness $witness) : self {
        if ($this->witnesses->removeElement($witness)) {
            // set the owning side to null (unless already changed)
            if ($witness->getPerson() === $this) {
                $witness->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents() : Collection {
        return $this->events;
    }

    public function addEvent(Event $event) : self {
        if ( ! $this->events->contains($event)) {
            $this->events[] = $event;
            $event->addParticipant($this);
        }

        return $this;
    }

    public function removeEvent(Event $event) : self {
        if ($this->events->removeElement($event)) {
            $event->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getFirstPartyTransactions() : Collection {
        return $this->firstPartyTransactions;
    }

    public function addFirstPartyTransaction(Transaction $firstPartyTransaction) : self {
        if ( ! $this->firstPartyTransactions->contains($firstPartyTransaction)) {
            $this->firstPartyTransactions[] = $firstPartyTransaction;
            $firstPartyTransaction->setFirstParty($this);
        }

        return $this;
    }

    public function removeFirstPartyTransaction(Transaction $firstPartyTransaction) : self {
        if ($this->firstPartyTransactions->removeElement($firstPartyTransaction)) {
            // set the owning side to null (unless already changed)
            if ($firstPartyTransaction->getFirstParty() === $this) {
                $firstPartyTransaction->setFirstParty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getSecondPartyTransactions() : Collection {
        return $this->secondPartyTransactions;
    }

    public function addSecondPartyTransaction(Transaction $secondPartyTransaction) : self {
        if ( ! $this->secondPartyTransactions->contains($secondPartyTransaction)) {
            $this->secondPartyTransactions[] = $secondPartyTransaction;
            $secondPartyTransaction->setSecondParty($this);
        }

        return $this;
    }

    public function removeSecondPartyTransaction(Transaction $secondPartyTransaction) : self {
        if ($this->secondPartyTransactions->removeElement($secondPartyTransaction)) {
            // set the owning side to null (unless already changed)
            if ($secondPartyTransaction->getSecondParty() === $this) {
                $secondPartyTransaction->setSecondParty(null);
            }
        }

        return $this;
    }
}
