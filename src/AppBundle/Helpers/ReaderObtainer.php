<?php

namespace AppBundle\Helpers;

use SplFileInfo;
use Ddeboer\DataImport\Reader\CsvReader;

class ReaderObtainer
{
    /**
     * Validation status
     * @bool
     */
    protected $valid;
    
    /**
     * Error message
     * @string
     */
    protected $message;
    
    /**
     * Array of CSV file headers
     * @array
     */
    protected $headers;
    
    /**
     * CsvValidator constructor.
     * @param array $headers
     */
    public function __construct($headers)
    {
        $this->valid = true;
        $this->message = '';
        $this->headers = $headers;
    }
    
    /**
     * @param $filePath
     * @return CsvReader|null
     */
    public function getReader($filePath)
    {
        if ($this->isExtValid($filePath)) {
            $reader = $this->initReader($filePath);
            if ($this->headersValidation($reader)) {
                return $reader;
            }
        }
        return null;
    }
    
    /**
     * @param $filePath
     * @return bool
     */
    public function isExtValid($filePath)
    {
        $fileInfo = new SplFileInfo($filePath);
        if ($fileInfo->getExtension() !== 'csv') {
            $this->valid = false;
            $this->message = 'incorrect file extension';
        }
        return $this->isValid();
    }
    
    /**
     * @param $filePath
     * @return CsvReader
     */
    public function initReader($filePath)
    {
        $file = new \SplFileObject($filePath);
        $csvReader = new CsvReader($file);
        $csvReader->setHeaderRowNumber(0);
        
        return $csvReader;
    }
    
    /**
     * @param $reader
     * @return bool
     */
    public function headersValidation($reader)
    {
        $csvHeaders = $reader->getColumnHeaders();
        foreach ($csvHeaders as $header) {
            if (!in_array($header, $this->headers))
                $this->valid = false;
        }
        if (!$this->valid)
            $this->message = 'csv file have incorrect headers';
        
        return $this->isValid();
    }
    
    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }
    
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}