<?php

namespace AppBundle\Workflow;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\EntityManager;


class ProductWorkflow extends Workflow
{
    protected $reader;
    protected $entityManager;
    
    public function __construct($reader, EntityManager $entityManager)
    {
        parent::__construct($reader);
        $this->reader = $reader;
        $this->entityManager = $entityManager;
    }
    
    public function temporary()
    {
        $doctrineWriter = new DoctrineWriter($this->entityManager, 'AppBundle:Product');
        $this->addWriter($doctrineWriter);
        $this->process();
    }
}