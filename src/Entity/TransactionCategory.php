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
 * TransactionCategory.
 *
 * @ORM\Table(name="transaction_category")
 * @ORM\Entity(repositoryClass="App\Repository\TransactionCategoryRepository")
 */
class TransactionCategory extends AbstractTerm
{
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
     * Add transaction.
     *
     * @return TransactionCategory
     */
    public function addTransaction(Transaction $transaction) {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTransaction(Transaction $transaction) {
        return $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions.
     *
     * @return Collection
     */
    public function getTransactions() {
        return $this->transactions;
    }
}
