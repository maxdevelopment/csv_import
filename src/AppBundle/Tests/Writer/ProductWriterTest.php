<?php

namespace AppBundle\Tests\Writer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductWriterTest extends KernelTestCase
{
    private $writer;

    /**
     * Set up writer test
     * @return void
     */
    public function setUp()
    {
        self::bootKernel();
        $this->writer = self::$kernel->getContainer()->get('app.product_writer');
    }

    /**
     * Test product creation
     * @return void
     */
    public function testCorrectProductCreation()
    {
        $productData = self::getProduct();
        $this->writer->writeItem($productData);
        $this->assertNull($this->writer->getErrors());
    }

    /**
     * Test incorrect product creation
     * @return void
     */
    public function testIncorrectProductCreation()
    {
        $productData = self::getProduct(false);
        $this->writer->writeItem($productData);
        $this->assertGreaterThan(0, count($this->writer->getErrors()));
    }

    /**
     * Test switch mode test/write
     * @return void
     */
    public function testIncorrectSetTest()
    {
        try {
            $this->writer->setTest('wrong');
        } catch (\Exception $e) {
            $this->assertContains('Not boolean value', $e->getMessage());
        }
    }

    /**
     * @param bool $correct
     * @return array Correct|Incorrect product
     */
    public static function getProduct($correct = true)
    {
        $productData = ['productCode' => 'P2030',
            'productName' => 'Test prod',
            'productDesc' => 'Mega prod for user',
            'price' => '20.50',
            'stock' => '15',
            'discontinued' => new \DateTime()];

        if ($correct) {
            return $productData;
        }

        $productData['price'] = '0.0';
        $productData['stock'] = '0';

        return $productData;
    }
}