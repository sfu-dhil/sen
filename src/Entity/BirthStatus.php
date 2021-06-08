<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\BirthStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * @ORM\Entity(repositoryClass=BirthStatusRepository::class)
 */
class BirthStatus extends AbstractTerm {
    /**
     * @var Collection|Person[]
     * @ORM\OneToMany(targetEntity="Person", mappedBy="birthStatus")
     */
    private $people;

    public function __construct() {
        parent::__construct();
        $this->people = new ArrayCollection();
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople() : Collection {
        return $this->people;
    }

    public function addPerson(Person $person) : self {
        if ( ! $this->people->contains($person)) {
            $this->people[] = $person;
            $person->setBirthStatus($this);
        }

        return $this;
    }

    public function removePerson(Person $person) : self {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getBirthStatus() === $this) {
                $person->setBirthStatus(null);
            }
        }

        return $this;
    }
}
