<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductsCsvImportCommand extends ContainerAwareCommand
{
    /**
     * Batch size for DoctrineWriter
     */
    const BATCH_SIZE = 10;
    
    /**
     * Configuring the Command
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('products:csv-import')
            ->setDescription('Import products from CSV file to DB')
            ->addArgument('file_path', InputArgument::REQUIRED, 'Valid .csv file for import')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Test .csv import without DB insert');
    }

    /**
     * Executing the Command
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('app.csv_import_logger');
        $logger->setIO($output, $input);

        $readerObtainer = $this->getContainer()->get('app.reader_opbtainer');
        $csvReader = $readerObtainer->getReader($input->getArgument('file_path'));

        if ($readerObtainer->isValid()) {
            $isTest = $input->getOption('test');
            if ($isTest) {
                $logger->setTestMode();
            }
            $productWorkflow = $this->getContainer()->get('app.product_workflow');
            $productWorkflow->setReader($csvReader);

            $productWriter = $this->getContainer()->get('app.product_writer');
            $productWriter->setTest($isTest);
            $productWriter->disableTruncate();
            $productWriter->setBatchSize(self::BATCH_SIZE);

            $result = $productWorkflow->runWorkflow($productWriter);

            $logger->logWork(
                $result->getSuccessCount(),
                $csvReader->getErrors(),
                $productWriter->getErrors()
            );
            return;
        }

        $output->writeln($readerObtainer->getMessage());
    }
}
