<?php

namespace AppBundle\Workflow;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Doctrine\ORM\EntityManager;




class ProductWorkflow extends Workflow
{
    protected $reader;
    protected $entityManager;
    protected $headers;

    public function __construct($reader, EntityManager $entityManager, $headers)
    {
        parent::__construct($reader);
        $this->reader = $reader;
        $this->entityManager = $entityManager;
        $this->headers = $headers;
    }

    public function temporary()
    {
        $doctrineWriter = new DoctrineWriter($this->entityManager, 'AppBundle:Product');

        /* headers and data converters */
        $this->addItemConverter(self::getMapper($this->headers));
        $this->addItemConverter(self::getValueConverter());

        $this->addWriter($doctrineWriter);
        $this->process();
    }

    public static function getMapper($headers)
    {
        $converter = new MappingItemConverter();
        $converter->addMapping($headers['code'], 'productCode');
        $converter->addMapping($headers['name'], 'productName');
        $converter->addMapping($headers['description'], 'productDesc');
        $converter->addMapping($headers['stock'], 'stock');
        $converter->addMapping($headers['price'], 'price');
        $converter->addMapping($headers['discontinued'], 'discontinued');

        return $converter;
    }

    public static function getValueConverter()
    {
        $converter = new CallbackItemConverter(function ($item) {

            foreach (['productCode', 'productName', 'productDesc'] as $key) {
                $item[$key] = mb_convert_encoding($item[$key], 'UTF-8');
            }

            $item['price'] = floatval($item['price']);
            $item['stock'] = intval($item['stock']);
            $item['discontinued'] = ($item['discontinued'] === 'yes') ? new \DateTime() : null;

            return $item;
        });

        return $converter;
    }
}