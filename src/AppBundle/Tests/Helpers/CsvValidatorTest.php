<?php

namespace AppBundle\Tests\Helpers;

use Ddeboer\DataImport\Reader\CsvReader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CsvValidatorTest extends KernelTestCase
{
    private $validator;
    private $resourcePath;
    
    /**
     * Set up validator test
     * @return void
     */
    public function setUp()
    {
        self::bootKernel();
        $this->validator = self::$kernel->getContainer()->get('app.csv_validator');
        $this->resourcePath = self::$kernel->locateResource('@AppBundle/TestResources');
    }
    
    /**
     * Test correct .csv file
     * @return void
     */
    public function testCorrectCsv()
    {
        $reader = $this->validator->validate($this->resourcePath . '/stock.csv');
        $this->assertNotNull($reader);
        $this->assertEquals($this->validator->isValid(), true);
        $this->assertEquals($this->validator->getMessage(), '');
        $this->assertInstanceOf(CsvReader::class, $reader);
    }
    
    /**
     * Test incorrect .csv file
     * @return void
     */
    public function testIncorrectCsv()
    {
        $reader = $this->validator->validate($this->resourcePath . '/error_stock.csv');
        $this->assertNull($reader);
        $this->assertEquals($this->validator->isValid(), false);
        $this->assertEquals(
            $this->validator->getMessage(), 'csv file have incorrect headers'
        );
    }
    
    /**
     * Test incorrect extension
     * @return void
     */
    public function testIncorrectExtension()
    {
        $reader = $this->validator->validate($this->resourcePath . '/text.txt');
        $this->assertNull($reader);
        $this->assertEquals($this->validator->isValid(), false);
        $this->assertEquals(
            $this->validator->getMessage(), 'incorrect file extension'
        );
    }
}