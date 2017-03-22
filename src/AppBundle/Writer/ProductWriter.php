<?php

namespace AppBundle\Writer;

use AppBundle\Entity\Product;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\EntityManager;

/**
 * Class PersistEntities
 *
 * @package AppBundle\Helpers
 */
class ProductWriter extends DoctrineWriter
{
    protected $validator;
    protected $test;
    protected $errors;
    protected $correct;

//    public function __construct(EntityManager $entityManager, $entityName)
//    {
//        parent::__construct($entityManager, $entityName);
//    }

    public function setParameters($validator, $test)
    {
        $this->validator = $validator;
        $this->test = $test;
        $this->prepare();
    }
    
    public function prepare()
    {
        $this->errors = [];
        $this->correct = [];
    }
    
    public function addCorrectProduct(Product $product)
    {
//        var_dump($product->getProductCode());
        $this->correct[$product->getProductCode()] = $product;
    }
    
    public function getCorrect()
    {
        return $this->correct;
    }
    
    public function addError($error)
    {
//        var_dump($error);
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