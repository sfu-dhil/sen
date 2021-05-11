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
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Race.
 * The name and label fields are the standard form from the spreadsheets.
 *
 * @ORM\Table(name="race")
 * @ORM\Entity(repositoryClass="App\Repository\RaceRepository")
 */
class Race extends AbstractTerm {

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $spanishUngendered;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $spanishMale;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $spanishFemale;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $frenchUngendered;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $frenchMale;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $frenchFemale;

    /**
     * @var Collection|Person[]
     * @ORM\OneToMany(targetEntity="Person", mappedBy="race")
     */
    private $people;

    public function __construct() {
        parent::__construct();
        $this->people = new ArrayCollection();
    }

    public function getSpanishUngendered(): ?string
    {
        return $this->spanishUngendered;
    }

    public function setSpanishUngendered(string $spanishUngendered): self
    {
        $this->spanishUngendered = $spanishUngendered;

        return $this;
    }

    public function getSpanishMale(): ?string
    {
        return $this->spanishMale;
    }

    public function setSpanishMale(string $spanishMale): self
    {
        $this->spanishMale = $spanishMale;

        return $this;
    }

    public function getSpanishFemale(): ?string
    {
        return $this->spanishFemale;
    }

    public function setSpanishFemale(string $spanishFemale): self
    {
        $this->spanishFemale = $spanishFemale;

        return $this;
    }

    public function getFrenchUngendered(): ?string
    {
        return $this->frenchUngendered;
    }

    public function setFrenchUngendered(string $frenchUngendered): self
    {
        $this->frenchUngendered = $frenchUngendered;

        return $this;
    }

    public function getFrenchMale(): ?string
    {
        return $this->frenchMale;
    }

    public function setFrenchMale(string $frenchMale): self
    {
        $this->frenchMale = $frenchMale;

        return $this;
    }

    public function getFrenchFemale(): ?string
    {
        return $this->frenchFemale;
    }

    public function setFrenchFemale(string $frenchFemale): self
    {
        $this->frenchFemale = $frenchFemale;

        return $this;
    }

}
