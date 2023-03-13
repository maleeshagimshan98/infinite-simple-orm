<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm;

use Infinite\SimpleOrm\Config\EntityConfiguration;
use Infinite\SimpleOrm\Config\EntityData;
use Infinite\SimpleOrm\Mapper;


class EntityManager {

    /**
     * Entity Configuration
     *
     * @var EntityConfiguration
     */
    private $entityConfiguration;       

    /**
     * array of repositories
     *
     * @var array
     */
    private $repositories = [];

    /**
     * data mapper
     * 
     * @var Mapper
     */
    private $mapper;

    /**
     * 
     * @var UnitOfWork
     */
    private $unitOfWork;

    public function __construct ()
    {        
        $this->entityConfiguration = new EntityConfiguration(); //... give configuration path and fully qualified class name of entities
        $this->mapper = new Mapper();
        $this->unitOfWork = new UnitOfWork($this->mapper,);
    }

    /**
     * set dependencies of repository class
     *
     * @param string $classname
     * @param mixed
     */
    private function setRepositoryDependencies (string $entityClassname, $repository) : mixed
    {
        //... set entity's class name (FQCN)
        //... set read manager
        //... set hydrator
        //... set change tracker
        return $repository;
    }

    /**
     * create a repositroy for the entity, either using given repository class or using the default class
     *
     * @param string $entityName
     * @return void
     */
    private function createRepository (string $entityName) : void
    {
        $repositoryClass = $this->entityConfiguration->get($entityName)->getRepositoryName();
        $entityClassname = $this->entityConfiguration->get($entityName)->getFqcn();
        if (!$repositoryClass) {
            $this->repositories[$entityName] = $this->setRepositoryDependencies($entityClassname,new Repository());
        }
        else {
            $this->repositories[$entityName] = $this->setRepositoryDependencies($entityClassname, new $repositoryClass());
        }   
    }

    /**
     * get the entity repository
     *
     * @param string $entityName
     * @return mixed
     */
    public function getRepository(string $entityName) : mixed
    {
        if (!isset($this->repositories[$entityName])) {
            $this->createRepository($entityName);
        }
        return $this->repositories[$entityName];
    }   

    /**
     * commit all the changes to the database
     *
     * @return void
     */
    public function flush() : void
    {        
    }
}