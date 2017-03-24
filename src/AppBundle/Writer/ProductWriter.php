<?php

namespace AppBundle\Writer;

use AppBundle\Entity\Product;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\EntityManager;

class ProductWriter extends DoctrineWriter
{
    protected $entityManager;
    protected $entityName;
    protected $validator;
    protected $test;
    protected $errors;
    protected $correct;

    /**
     * ProductWriter constructor.
     * @param EntityManager $entityManager
     * @param string $entityName
     * @param array|null|string $validator
     */
    public function __construct(EntityManager $entityManager, $entityName, $validator)
    {
        parent::__construct($entityManager, $entityName);
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
        $this->validator = $validator;
    }

    public function setTest($state)
    {
        if (is_bool($state)) {
            $this->test = $state;
            return;
        }
        throw new \Exception(sprintf('Not boolean value [%s]', __CLASS__ . ' function: ' . __FUNCTION__));
    }

    public function isTest()
    {
        return $this->test;
    }

    public function prepare()
    {
        $this->errors = [];
        $this->correct = [];
    }

    public function addCorrectProduct(Product $product)
    {
        $this->correct[$product->getProductCode()] = $product;
    }

    public function getCorrect()
    {
        return $this->correct;
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function writeItem(array $item)
    {
        $entity = $this->findOrCreateItem($item);
        $this->loadAssociationObjectsToEntity($item, $entity);
        $this->updateEntity($item, $entity);
        $errors = $this->validator->validate($entity);
        if (!$errors->has(0)) {
            $this->addCorrectProduct($entity);
        } else {
            $this->addError($item);
        }
    }

    public function flush()
    {
        if (!$this->test) {
            $em = $this->entityManager;
            foreach ($this->correct as $product) {
                $em->persist($product);
            }
            $em->flush();
        }
    }
}