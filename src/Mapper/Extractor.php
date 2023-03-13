<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Mapper;

class Extractor {

    /**
     * Entity Configuration
     *
     * @var EntityConfiguration
     */
    private $entityConfiguration;

    public function __construct (EntityConfiguration $entityConfiguration)
    { 
        $this->entityConfiguration = $entityConfiguration;       
    }
   

    private function setValues () : void
    {

    }

    public function extract (string $name, $data) : array
    {
        
    }
}