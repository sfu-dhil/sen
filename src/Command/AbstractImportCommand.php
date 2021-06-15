<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Services\ImportService;
use App\Util\NotaryColumnDefinitions as N;
use App\Util\SacramentColumnDefinitions as S;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractImportCommand extends Command {
    protected EntityManagerInterface $em;

    protected ImportService $importer;

    public function __construct($name = null) {
        parent::__construct($name);
    }

    abstract protected function process(array $row) : void;

    protected function configure() : void {
        $this->addArgument('files', InputArgument::IS_ARRAY, 'List of CSV files to import');
        $this->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1);
    }

    protected function preprocess(array $row) : array {
        $data = mb_convert_encoding($row, 'UTF-8', 'UTF-8');
        $data = array_map(static fn($d) => preg_replace('/^\\s+|\\s+$/u', '', $d), $data);

        return array_pad($data, max(N::row_count, S::row_count), '');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $files = $input->getArgument('files');
        $skip = (int) $input->getOption('skip');

        foreach ($files as $file) {
            $this->import($file, $skip, $output);
        }

        return 0;
    }

    public function countLines($file) : int {
        $f = fopen($file, 'r');
        $lines = 0;
        while( ! feof($f)) {
            $lines += substr_count(fread($f, 8192), "\n");
        }
        fclose($f);
        return $lines;
    }

    /**
     * @throws Exception
     */
    public function import(string $file, int $skip, OutputInterface $output) : void {
        $output->writeln($file);
        $lines = $this->countLines($file);
        $progressBar = new ProgressBar($output, $lines);
        $progressBar->start();

        $handle = fopen($file, 'r');

        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
            $progressBar->advance();
        }
        while ($row = fgetcsv($handle)) {
            $data = $this->preprocess($row);
            try {
                $this->process($data);
                $this->em->beginTransaction();
                $this->em->flush();
                $this->em->commit();
            } catch (Exception $e) {
                $output->writeln("{$file}:{$i} - {$e->getMessage()}");
                $this->em->rollback();
                $this->em->clear();
            }
            $progressBar->advance();
        }
        $output->writeln("");
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
    public function setImportService(ImportService $importService) : void {
        $this->importer = $importService;
    }
}
