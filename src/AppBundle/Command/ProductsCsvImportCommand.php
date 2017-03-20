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
     * Configuring the Command
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('products:csv-import')
            ->setDescription('Import products from CSV file to DB')
            ->addArgument('file_path', InputArgument::REQUIRED, 'Valid .csv file for import')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Test .csv import without DB insert')
        ;
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
        $csvValidator = $this->getContainer()->get('app.csvValidator');
        $csvReader = $csvValidator->validate($input->getArgument('file_path'));
    }

}
