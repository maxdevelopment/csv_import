<?php

namespace AppBundle\Tests\Helpers;

use Ddeboer\DataImport\Reader\CsvReader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReaderObtainerTest extends KernelTestCase
{
    private $csvReader;
    private $resourcePath;
    
    /**
     * Set up validator test
     * @return void
     */
    public function setUp()
    {
        self::bootKernel();
        $this->csvReader = self::$kernel->getContainer()->get('app.reader_opbtainer');
        $this->resourcePath = self::$kernel->locateResource('@AppBundle/TestResources');
    }
    
    /**
     * Test correct .csv file
     * @return void
     */
    public function testCorrectCsv()
    {
        $reader = $this->csvReader->getReader($this->resourcePath . '/stock.csv');
        $this->assertNotNull($reader);
        $this->assertEquals($this->csvReader->isValid(), true);
        $this->assertEquals($this->csvReader->getMessage(), '');
        $this->assertInstanceOf(CsvReader::class, $reader);
    }
    
    /**
     * Test incorrect .csv file
     * @return void
     */
    public function testIncorrectCsv()
    {
        $reader = $this->csvReader->getReader($this->resourcePath . '/error_stock.csv');
        $this->assertNull($reader);
        $this->assertEquals($this->csvReader->isValid(), false);
        $this->assertEquals(
            $this->csvReader->getMessage(), 'csv file have incorrect headers'
        );
    }
    
    /**
     * Test incorrect extension
     * @return void
     */
    public function testIncorrectExtension()
    {
        $reader = $this->csvReader->getReader($this->resourcePath . '/text.txt');
        $this->assertNull($reader);
        $this->assertEquals($this->csvReader->isValid(), false);
        $this->assertEquals(
            $this->csvReader->getMessage(), 'incorrect file extension'
        );
    }
}