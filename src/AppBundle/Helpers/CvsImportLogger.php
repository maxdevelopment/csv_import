<?php

namespace AppBundle\Helpers;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CvsImportLogger
{
    protected $io;
    protected $test = false;

    /**
     * @param OutputInterface $output
     * @param InputInterface $input
     */
    public function setIO(OutputInterface $output, InputInterface $input)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    public function setTestMode()
    {
        $this->test = true;
    }

    /**
     * Log work to Output Interface
     *
     * @param int $processed Number of products passed to writer
     * @param array $csvErrors Failed to read lines
     * @param array $insertErrors Failed to insert lines
     *
     * @return void
     */
    public function logWork($processed, $csvErrors, $insertErrors)
    {
        $io = $this->io;
        $io->title('Product CSV import status');
        if ($this->test) {
            $io->warning('[Test Mode] without database writer');
        }
        $io->newLine();
        $io->success(($processed + count($csvErrors)) . ' product(s) have been processed');
        $io->comment(($processed - count($insertErrors)) . ' product(s) have been correctly added');
        $io->comment(count($insertErrors) + count($csvErrors) . ' line(s) have been skipped:');

        $io->section('Failed to read:');
        $io->listing(array_map(function ($value) {
            return implode(' ', $value);
        }, $csvErrors));

        $io->section('Failed to insert:');
        $io->listing(array_map(function ($value) {
            return
                $value['productCode'] . ' ' .
                $value['productName'] . ' ' .
                $value['productDesc'] . ' ' .
                $value['stock'] . ' ' .
                $value['price'];
        }, $insertErrors));
    }
}