<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Race.
 *
 * @ORM\Table(name="race")
 * @ORM\Entity(repositoryClass="App\Repository\RaceRepository")
 */
class Race extends AbstractTerm {
    /**
     * @var Collection|Person[]
     * @ORM\OneToMany(targetEntity="Person", mappedBy="race")
     */
    private $people;

    public function __construct() {
        parent::__construct();
        $this->people = new ArrayCollection();
    }

    /**
     * Add person.
     *
     * @return Race
     */
    public function addPerson(Person $person) {
        $this->people[] = $person;

        return $this;
    }

    /**
     * Remove person.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePerson(Person $person) {
        return $this->people->removeElement($person);
    }

    /**
     * Get people.
     *
     * @return Collection
     */
    public function getPeople() {
        return $this->people;
    }
}
