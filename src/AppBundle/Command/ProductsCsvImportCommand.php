<?php

namespace AppBundle\Command;

use AppBundle\Workflow\ProductWorkflow;
use AppBundle\Writer\ProductWriter;
use AppBundle\Writer\NewProductWriter;
use AppBundle\Helpers\CsvImportLogger;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductsCsvImportCommand extends ContainerAwareCommand
{
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
        $csvValidator = $this->getContainer()->get('app.csv_validator');
        $csvReader = $csvValidator->validate($input->getArgument('file_path'));
        if ($csvValidator->isValid()) {
            $isTest = $input->getOption('test');
            if ($isTest) {
                $output->writeln(
                    '[test mode] without database writer'
                );
            }
            $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $headers = $this->getContainer()->getParameter('product.headers');
            $productWorkflow = new ProductWorkflow($csvReader, $entityManager, $headers);
            $productWriter = $this->setWriter($entityManager, $isTest);

            $result = $productWorkflow->runWorkflow($output, $productWriter);
//            $result = $productWorkflow->temporary($productWriter);


            $newProductWriter = $this->getContainer()->get('app.new_product_writer');
            var_dump($newProductWriter->getEntityName());


            $productWriter->flush();

            $logger = new CsvImportLogger($output);
            $logger->logWork(
                $result->getSuccessCount(),
                $csvReader->getErrors(),
                $productWriter->getErrors()
            );
        } else {
            $output->writeln($csvValidator->getMessage());
        }
    }

    protected function setWriter($entityManager, $isTest)
    {
        $writer = new ProductWriter($entityManager, 'AppBundle:Product');
        $validator = $this->getContainer()->get('validator');
        $writer->setParameters($validator, $isTest);
        return $writer;
    }

}
