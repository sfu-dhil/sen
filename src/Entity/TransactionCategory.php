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
 * TransactionCategory.
 *
 * @ORM\Table(name="transaction_category")
 * @ORM\Entity(repositoryClass="App\Repository\TransactionCategoryRepository")
 */
class TransactionCategory extends AbstractTerm {
    /**
     * @var Collection|Transaction[]
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="category")
     */
    private $transactions;

    public function __construct() {
        parent::__construct();
        $this->transactions = new ArrayCollection();
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions() : Collection {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction) : self {
        if ( ! $this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setCategory($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction) : self {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCategory() === $this) {
                $transaction->setCategory(null);
            }
        }

        return $this;
    }
}
