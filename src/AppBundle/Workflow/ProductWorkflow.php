<?php

namespace AppBundle\Workflow;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Doctrine\ORM\EntityManager;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;


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

//    public function temporary($wrt)
//    {
//        $doctrineWriter = new DoctrineWriter($this->entityManager, 'AppBundle:Product');
//
//        /* headers and data converters */
//        try {
//            $em = $this->entityManager;
//            $em->getConnection()->beginTransaction();
//            $result = $this
//                ->addItemConverter(self::getMapper($this->headers))
//                ->addItemConverter(self::getValueConverter())
//                ->addWriter($doctrineWriter)
//                ->process();
//            $em->getConnection()->commit();
//        } catch (Exception $e) {
//            echo 'Exception CALLED';
//            $em->getConnection()->rollBack();
//            throw $e;
//        }
//        return $result;
//    }

    public function runWorkflow(OutputInterface $logOutput, $writer)
    {

        $em = $this->entityManager;
        $em->getConnection()->beginTransaction();
        try {
            $result = $this
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