<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
 */
declare (strict_types = 1);

namespace Infinite\SimpleOrm;

class Repository
{

    /**
     * class name of entities
     *
     * @var string
     */
    protected $className;

    /**
     * Undocumented variable
     *
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * data mapper
     * 
     * @var Mapper
     */
    private $mapper;

    public function __construct(IdentityMap $identityMap, Mapper $mapper)
    {    
        $this->identityMap = $identityMap;
        $this->mapper = $mapper;    
    }

    /**
     * find an entity, store it in the instance, add it to change tracker,
     *
     * @param string $entityName
     * @param array $condition
     * @return array
     */
    final public function find(string $entityName, array $condition = []): array
    {
        //... find in the identity map
        //... if found, return it
        //... else get it from database using mapper

        $entities = $this->mapper->get($entityName,$condition);
        return [$entities];
    }

    /**
     * add an entity to repository
     *
     * @param mixed $entity
     * @return void
     */
    public function add($entity): void
    {
        if (\get_class($entity) !== $this->className) {
            throw new \Exception("type of the passed entity does not match with the repository's entity type");
        }
        $this->changeTracker->add($entity);
        $this->entities[] = $entity;        
    }

    public function remove(): void
    {

    }

}
