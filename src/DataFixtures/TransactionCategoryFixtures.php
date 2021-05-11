<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\TransactionCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory.
 *
 * @author michael
 */
class TransactionCategoryFixtures extends Fixture {
    public function load(ObjectManager $manager) : void {
        $category = new TransactionCategory();
        $category->setName('sale-property');
        $category->setLabel('Sale of property');
        $manager->persist($category);
        $this->setReference('transactioncategory.1', $category);
        $manager->flush();
    }
}
