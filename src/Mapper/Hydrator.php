<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Mapper;

use Infinite\SimpleOrm\Config\EntityConfiguration;

class Hydrator {

    /**
     * Entity Configuration
     *
     * @var EntityConfiguration
     */
    private $entityConfiguration;

    /**
     * entity which is being hydrated
     * 
     * @var object
     */
    private $entity;

    private $clone = [];

    public function __construct (EntityConfiguration $entityConfiguration)
    { 
        $this->entityConfiguration = $entityConfiguration;       
    }
    
    private function setClone ($name,$data) : void
    {
        $this->clone[$name] = $data;
    }

    /**
     * set properties of a given entity using entity attributes
     */
    private function setAttributeValues (array $attributes,array $data,$entity,Proxy $proxy) : array
    {
        $clone = [];
        foreach ($attributes as $name => $attribute) {
            $columnName = $attribute->getColumnName();
            //... check isset($data[$columnName])
            $clone[$name] = $data[$columnName];            
            $proxy->setValue($name,$entity,$data[$columnName]);
        }
        return $clone;
    }

    private function setAssociatedEntity ($name,array $data) : array
    {
        $associatedEntityData = $this->entityConfiguration->get($name);
        $associatedEntityAttributes = $associatedEntityData->getAttributes();
        $associatedFqcn = $associatedEntityData->getFqcn();
        $assocEntity = new $associatedFqcn();
        $associatedClone = $this->setAttributeValues($associatedEntityAttributes,$data,$assocEntity,$proxy);
        return $associatedClone;
    }

    /**
     * hydrate data into the given entity object
     * 
     * @param string $name
     * @param array $data
     * @return object
     */
    public function hydrate (string $name, array $data) : MapperResult
    {
        $entityData = $this->entityConfiguration->get($name);
        $fullyQualifiedClassName = $entityData->getFqcn($name);
        $this->entity = new $fullyQualifiedClassName();

        //... set attribute values and main entity clone
        $this->clone = $this->setAttributeValues($entityData->getAttributes(),$data,$this->entity,$proxy);

        $eagerAssoc = $entityData->getEagerAssoc();
        
        //... set associated entities
        if(count($eagerAssoc) > 0) {
            foreach ($eagerAssoc as $assoc) {
               $this->clone[$assoc] = $this->setAssociatedEntity($assoc,$data);
            }
        }

        return new MapperResult($this->entity,$this->clone);
    }
}