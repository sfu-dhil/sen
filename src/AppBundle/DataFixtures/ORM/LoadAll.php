<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nines\BlogBundle\DataFixtures\ORM\LoadPage;
use Nines\BlogBundle\DataFixtures\ORM\LoadPost;
use Nines\BlogBundle\DataFixtures\ORM\LoadPostCategory;
use Nines\BlogBundle\DataFixtures\ORM\LoadPostStatus;
use Nines\DublinCoreBundle\DataFixtures\ORM\LoadElement;
use Nines\FeedbackBundle\DataFixtures\ORM\LoadComment;
use Nines\FeedbackBundle\DataFixtures\ORM\LoadCommentNote;
use Nines\FeedbackBundle\DataFixtures\ORM\LoadCommentStatus;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;

/**
 * Description of LoadAll
 *
 * @author mjoyce
 */
class LoadAll extends Fixture implements DependentFixtureInterface {

    //put your code here
    public function load(ObjectManager $manager) {

    }

    public function getDependencies(): array {
        return array(
            LoadCity::class,
            LoadEvent::class,
            LoadEventCategory::class,
            LoadLedger::class,
            LoadLocation::class,
            LoadLocationCategory::class,
            LoadNotary::class,
            LoadPerson::class,
            LoadRace::class,
            LoadRelationship::class,
            LoadRelationshipCategory::class,
            LoadResidence::class,
            LoadTransaction::class,
            LoadTransactionCategory::class,
            LoadWitness::class,
            LoadWitnessCategory::class,
            LoadPage::class,
            LoadPost::class,
            LoadPostCategory::class,
            LoadPostStatus::class,
            LoadElement::class,
            LoadComment::class,
            LoadCommentNote::class,
            LoadCommentStatus::class,
            LoadUser::class,
        );
    }

}
