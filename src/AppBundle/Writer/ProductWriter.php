<?php

namespace AppBundle\Writer;

use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\EntityManager;

class ProductWriter extends DoctrineWriter
{
    protected $validator;
    protected $test;

    public function __construct(EntityManager $entityManager, $entityName)
    {
        parent::__construct($entityManager, $entityName);
    }

    public function setParameters($validator, $test)
    {
        $this->validator = $validator;
        $this->test = $test;
        $this->prepare();
    }

}