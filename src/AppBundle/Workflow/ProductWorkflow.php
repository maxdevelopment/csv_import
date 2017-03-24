<?php

namespace AppBundle\Workflow;

use Doctrine\ORM\EntityManager;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Exception;

class ProductWorkflow
{
    protected $workflow;
    protected $entityManager;
    protected $headers;

    /**
     * ProductWorkflow constructor.
     * @param EntityManager $entityManager
     * @param $headers
     */
    public function __construct(EntityManager $entityManager, $headers)
    {
        $this->entityManager = $entityManager;
        $this->headers = $headers;
    }

    /**
     * @param $reader
     */
    public function setReader($reader)
    {
        $this->workflow = new Workflow($reader);
    }

    /**
     * @param $writer
     * @return mixed
     * @throws Exception
     */
    public function runWorkflow($writer)
    {

        $em = $this->entityManager;
        $em->getConnection()->beginTransaction();
        try {
            $result = $this->workflow
                ->addItemConverter(self::getMapper($this->headers))
                ->addItemConverter(self::getValueConverter())
                ->addWriter($writer)
                ->process();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
        return $result;
    }

    /**
     * @param $headers
     * @return MappingItemConverter
     */
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

    /**
     * @return CallbackItemConverter
     */
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