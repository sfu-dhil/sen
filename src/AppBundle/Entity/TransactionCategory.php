<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransactionCategory
 *
 * @ORM\Table(name="transaction_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionCategoryRepository")
 */
class TransactionCategory extends \Nines\UtilBundle\Entity\AbstractTerm
{
    /**
     * @var Collection|Transaction[]
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="category")
     */
    private $transactions;
}
