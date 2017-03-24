<?php

namespace AppBundle\Tests\Writer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductWriterTest extends KernelTestCase
{
    private $writer;

    public function setUp()
    {
        self::bootKernel();
        $this->writer = self::$kernel->getContainer()->get('app.product_writer');
    }

    public function testCorrectProductCreation()
    {
        $productData = self::getProduct();
        $this->writer->writeItem($productData);
        $product = $this->writer->getCorrect()['P2030'];
        $this->assertEquals(
            $productData['productCode'], $product->getProductCode()
        );
        $this->assertEquals(
            $productData['productName'], $product->getProductName()
        );
        $this->assertEquals(
            $productData['productDesc'], $product->getProductDesc()
        );
        $this->assertEquals($productData['price'], $product->getPrice());
        $this->assertEquals($productData['stock'], $product->getStock());
        $this->assertNotNull($product->getDiscontinued());

    }

    public function testIncorrectProductCreation()
    {
        $productData = self::getProduct(false);
        $this->writer->writeItem($productData);
        $this->assertGreaterThan(0, count($this->writer->getErrors()));
        $this->assertNull($this->writer->getCorrect());

    }

    public function testIncorrectSetTest()
    {
        try {
            $this->writer->setTest('wrong');
        } catch (\Exception $e) {
            $this->assertContains('Not boolean value', $e->getMessage());
        }
    }

    public static function getProduct($correct = true)
    {
        $productData = ['productCode' => 'P2030',
            'productName' => 'Test prod',
            'productDesc' => 'Mega prod for user',
            'price' => '20.50',
            'stock' => '15',
            'discontinued' => 'yes'];

        if ($correct)
            return $productData;

        $productData['price'] = '0.0';
        $productData['stock'] = '0';

        return $productData;
    }
}