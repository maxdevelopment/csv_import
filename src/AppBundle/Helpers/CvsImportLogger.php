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
        if ($this->test)
            $io->warning('[Test Mode] without database writer');
        $io->newLine();
        $io->success(($processed + count($csvErrors)) . ' product(s) have been processed');
        $io->comment(($processed - count($insertErrors)) . ' product(s) have been correctly added');
        $io->comment(count($insertErrors) + count($csvErrors) . ' line(s) have been skipped:');

        $io->section('Failed to read:');
        $err_r = [];
        foreach ($csvErrors as $error) {
            $err_r[] = implode(' ', $error);
        }
        $io->listing($err_r);

        $io->section('Failed to insert:');
        $err_i = [];
        foreach ($insertErrors as $error) {
            $err_i[] =
                $error['productCode'] . ' ' .
                $error['productName'] . ' ' .
                $error['productDesc'] . ' ' .
                $error['stock'] . ' ' .
                $error['price']
            ;
        }
        $io->listing($err_i);
    }
}