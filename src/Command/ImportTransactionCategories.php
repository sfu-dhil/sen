<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\TransactionCategory;
use App\Repository\TransactionCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportTransactionCategories extends Command {
    private TransactionCategoryRepository $repo;

    private EntityManagerInterface $em;

    protected static $defaultName = 'sen:import:transaction-categories';

    protected static $defaultDescription = 'Add a short description for your command';

    protected function configure() : void {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of files to import')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Rows of data to skip', 1);
    }

    protected function import($file, $skip) : void {
        $handle = fopen($file, 'r');
        for ($i = 0; $i < $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {
            $standard = $row[0];
            $category = $this->repo->findOneBy(['name' => $standard]);
            if ( ! $category) {
                $category = new TransactionCategory();
                $category->setName($standard);
                $category->setLabel(mb_convert_case($standard, \MB_CASE_TITLE));
                $this->em->persist($category);
            }
            $category->setDescription($row[1]);
            $this->em->flush();
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $files = $input->getArgument('files');
        $skip = $input->getOption('skip');

        foreach ($files as $file) {
            $this->import($file, $skip);
        }

        return 0;
    }

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }

    /**
     * @required
     */
    public function setTransactionCategoryRepository(TransactionCategoryRepository $repo) : void {
        $this->repo = $repo;
    }
}
