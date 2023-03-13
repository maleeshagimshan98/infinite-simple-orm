<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm;

class IdentityMap {

    /**
     * array of entities
     *
     * @var array
     */
    private $entities = [];

    /**
     * array of cloned entities used to check if anything has changed
     *
     * @var array
     */
    private $clonedEntities = [];


    public function __construct ()
    {        
    }

    private function addProxy () : void
    {        
    }

    /**
     * add entity to track it's changes
     *
     * @param array|mixed $entity
     * @return void
     */
    public function add ($entity) : void
    {
        $className = \get_class($entity);

        //... check if this entity already exists before adding into $this->entities

        if (isset($this->entities[$className])) {
            array_push($this->entities[$className],$entity->__clone());
        }
        else {
        }        
    }

    /**
     * get an entity from Identity Map
     *
     * @param string $entity
     * @param array $condition
     * @return mixed
     */
    public function get (string $entity, array $condition) : mixed
    {

    }

    public function getProxy (string $name) : object
    {
        //... return a reflection class instance of the given entity class
        return (object) [];
    }
    
    


}