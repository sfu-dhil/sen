<?php

namespace AppBundle\Services;

use AppBundle\Entity\City;
use AppBundle\Entity\Ledger;
use AppBundle\Entity\Notary;
use AppBundle\Entity\Person;
use AppBundle\Entity\Race;
use AppBundle\Entity\Relationship;
use AppBundle\Entity\RelationshipCategory;
use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionCategory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Description of AbstractImportService
 *
 * @author michael
 */
class ImportService {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Construct the import service.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Find a notary by name. Creates a new notary record if necessary.
     *
     * @param string $name
     * @return Notary
     */
    public function findNotary($name) {
        $repo = $this->em->getRepository(Notary::class);
        $notary = $repo->findOneBy(array(
            'name' => $name,
        ));
        if (!$notary) {
            $notary = new Notary();
            $notary->setName($name);
            $this->em->persist($notary);
        }
        return $notary;
    }

    /**
     * Find or create a ledger for a notary with the given volume identifier and
     * year.
     *
     * @param Notary $notary
     * @param string $volume
     * @param integer $year
     * @return Ledger
     */
    public function findLedger(Notary $notary, $volume, $year) {
        $repo = $this->em->getRepository(Ledger::class);
        $ledger = $repo->findOneBy(array(
            'volume' => $volume,
            'notary' => $notary,
        ));
        if (!$ledger) {
            $ledger = new Ledger();
            $ledger->setNotary($notary);
            $ledger->setVolume($volume);
            $ledger->setYear($year);
            $this->em->persist($ledger);
        }
        return $ledger;
    }

    /**
     * Find or create a race record by name.
     *
     * @param string $name
     * @return Race
     */
    public function findRace($name) {
        if (!$name) {
            return null;
        }
        $repo = $this->em->getRepository(Race::class);
        $race = $repo->findOneBy(array(
            'name' => $name,
        ));
        if (!$race) {
            $race = new Race();
            $race->setName($name);
            $race->setLabel(ucwords($name));
            $this->em->persist($race);
        }
        return $race;
    }

    /**
     * Find or create a record for a person.
     *
     * @param string $given
     * @param string $family
     * @param string $raceName
     * @param string $status
     * @return Person
     */
    public function findPerson($given, $family, $raceName = '', $status = '') {
        $repo = $this->em->getRepository(Person::class);
        $person = $repo->findOneBy(array(
            'firstName' => $given,
            'lastName' => strtoupper($family),
        ));
        $race = $this->findRace($raceName);
        if (!$person) {
            $person = new Person();
            $person->setFirstName($given);
            $person->setLastName($family);
            $person->setRace($race);
            $person->setStatus($status);
            $this->em->persist($person);
        }
        if ($person->getRace() && $person->getRace()->getName() !== $raceName) {
            $this->logger->warn("Possible duplicate person: {$person} with races {$person->getRace()->getName()} and {$raceName}");
        }
        if ($person->getStatus() !== $status) {
            $this->logger->warn("Possible duplicate person: {$person} with statuses {$person->getStatus()} and {$status}");
        }
        return $person;
    }

    public function findCity($name) {
        $repo = $this->em->getRepository(City::class);
        $city = $repo->findOneBy(array(
            'name' => $name,
        ));
        if( ! $city) {
            $city = new City();
            $city->setName($name);
            $this->em->persist($city);
        }
        return $city;
    }

    /**
     * Find or create a transaction category by label.
     *
     * @param string $label
     * @return TransactionCategory
     */
    public function findTransactionCategory($label) {
        $repo = $this->em->getRepository(TransactionCategory::class);
        $category = $repo->findOneBy(array(
            'label' => $label,
        ));
        if (!$category) {
            $short = preg_replace("/[^a-z0-9]/u", "-", strtolower($label));
            $category = new TransactionCategory();
            $category->setName($short);
            $category->setLabel($label);
            $this->em->persist($category);
        }
        return $category;
    }

    /**
     * Find or create a relationship category by name.
     *
     * @param string $name
     * @return RelationshipCategory
     */
    public function findRelationshipCategory($name) {
        $repo = $this->em->getRepository(RelationshipCategory::class);
        $category = $repo->findOneBy(array(
            'name' => $name,
        ));
        if (!$category) {
            $category = new RelationshipCategory();
            $category->setName($name);
            $category->setLabel(ucwords($name));
            $this->em->persist($category);
        }
        return $category;
    }

    /**
     * Create a transaction.
     *
     * @param Ledger $ledger
     * @param Person $firstParty
     * @param Person $secondParty
     * @param array $row
     */
    public function createTransaction(Ledger $ledger, Person $firstParty, Person $secondParty, $row) {
        $transaction = new Transaction();
        $transaction->setLedger($ledger);
        $transaction->setFirstParty($firstParty);
        $transaction->setFirstPartyNote($row[9]);
        if ($row[8]) {
            $firstSpouse = $this->findPerson($row[8], $firstParty->getLastName());
            $firstRelationship = new Relationship();
            $firstRelationship->setCategory($this->findRelationshipCategory('spouse'));
            $firstRelationship->setPerson($firstParty);
            $firstRelationship->setRelation($firstSpouse);
            $this->em->persist($firstRelationship);
        }
        $transaction->setConjunction($row[10]);
        $transaction->setSecondParty($secondParty);
        if ($row[15]) {
            $secondSpouse = $this->findPerson($row[15], $secondParty->getLastName());
            $secondRelationship = new Relationship();
            $secondRelationship->setCategory($this->findRelationshipCategory('spouse'));
            $secondRelationship->setPerson($secondParty);
            $secondRelationship->setRelation($secondSpouse);
            $this->em->persist($secondRelationship);
        }
        $transaction->setSecondPartyNote($row[16]);
        $transaction->setCategory($this->findTransactionCategory($row[17]));
        $transaction->setDate(new DateTime($row[18] . '-' . $row[3]));
        $transaction->setPage($row[19]);
        $transaction->setNotes($row[20]);
        $this->em->persist($transaction);
    }

}
