<?php

namespace AppBundle\Writer;

use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\EntityManager;

class NewProductWriter extends DoctrineWriter
{
//    public function __construct(EntityManager $entityManager, $entityName, ContainerAwareCommand $command)
//    {
//        parent::__construct($entityManager, $entityName);
//    }

    protected $entityManager;
    protected $entityName;

    public function __construct(EntityManager $entityManager, $entityName)
    {
        parent::__construct($entityManager, $entityName);
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
    }

    public function getManager()
    {
        return $this->entityManager;
    }

    public function getEntityName()
    {
        return $this->entityName;
    }

}