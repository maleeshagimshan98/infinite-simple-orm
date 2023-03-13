<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm;

class UnitOfWork {

    /**
     * identity map
     * 
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * Tracks changes of entities
     *
     * @var Mapper
     */
    private $mapper;

    /**
     * newly created entities
     */
    private $new = [];

    /**
     * entities that reside on database, but not changed in the current request
     */
    private $fresh = [];

    /**
     * entities that were changed in the current request
     */
    private $dirty = [];


    public function __construct (Mapper $mapper,IdentityMap $identityMap)
    {
        $this->identityMap = $identityMap;
        $this->mapper = $mapper;        
    }

    private function addNew ()
    {
    }

    private function addDirty ()
    {
        //... entities that are already retrieved from database
    }

    public function flush ()
    {

    }
}