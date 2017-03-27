<?php

namespace AppBundle\Writer;

use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\EntityManager;

class ProductWriter extends DoctrineWriter
{
    protected $entityManager;
    protected $entityName;
    protected $validator;
    protected $test;
    protected $errors;
    
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
    }
    
    public function writeItem(array $item)
    {
        $entity = $this->findOrCreateItem($item);
        $this->loadAssociationObjectsToEntity($item, $entity);
        $this->updateEntity($item, $entity);
        $errors = $this->validator->validate($entity);
        if (!$errors->has(0)) {
            $this->counter++;
            $this->entityManager->persist($entity);
            if (($this->counter % $this->batchSize) == 0 && !$this->isTest()) {
                $this->flushAndClear();
            }
        } else {
            $this->addError($item);
        }
    }
    
    public function addError($error)
    {
        $this->errors[] = $error;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function finish()
    {
        if (!$this->isTest()) {
            $this->flushAndClear();
        }
    }
}