<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm;

use Infinite\SimpleOrm\DbConnection;
use Infinite\SimpleOrm\Query\QueryBuilder;
use Infinite\SimpleOrm\Query\QueryBuilderHelper;
use Infinite\SimpleOrm\Query\QueryDb;
use Infinite\SimpleOrm\Mapper\ReadManager;
use Infinite\SimpleOrm\Mapper\WriteManager;
use Infinite\SimpleOrm\Mapper\Hydrator;
use Infinite\SimpleOrm\Mapper\Extractor;
use Infinite\SimpleOrm\IdentityMap;

/**
 * =============================
 * Class Responsibilities
 * 
 * 01. transfer data between entity objects and database
 * 
 * =============================
 */

class Mapper
{
    
    /**
     * Database connection
     *
     * @var DbConnection
     */
    private $dbConnection;

    /**
     * Query Builder
     *
     * @var [type]
     */
    private $queryBuilder;

    /**
     * database abstraction layer
     * performs queries and transactions, fetches results
     *
     * @var QueryDb
     */
    private $queryDb;

    /**
     * Entity Configuration
     *
     * @var EntityConfiguration
     */
    private $entityConfiguration;
    
    /**
     * inherit doc
     *
     * @var Hydrator
     */
    private $hydrator;

    /**
     * inherit doc
     *
     * @var ReadManager
     */
    private $readManager;

    /**
     * inherit doc
     *
     * @var WriteManager
     */
    private $writeManager;

    /**
     * inherit doc
     *
     * @var IdentityMap
     */
    private $identityMap;    

    public function __construct ($dbConfig, $entityConfiguration, IdentityMap $identityMap)
    {
        $this->dbConnection = DbConnection::getConnection(); //... pass database connection configuration
        $this->entityConfiguration = $entityConfiguration;        
        $this->queryBuilder = new QueryBuilder();
        $this->queryDb = new QueryDb($this->dbConnection); //... give database connection
        $this->readManager = new ReadManager($this->entityConfiguration,$this->queryBuilder,$this->queryDb);
        $this->writeManager = new WriteManager($this->entityConfiguration,$this->queryBuilder,$this->queryDb);
        $this->hydrator = new Hydrator($this->entityConfiguration);
        $this->identityMap = $identityMap;
    }    

    /**
     * fetch data from database, hydrate data in entity class, add to UnitOfWork
     *
     * @param string $entityName
     * @param array $condition
     * @return array
     */
    public function get (string $entityName, array $condition = []) : array
    {                
        $results = $this->readManager->getEntity($entityName,$condition);
        $proxy = $this->identityMap->getProxy($entityName);

        if (count($results) > 0) {
            $entities = [];
            foreach ($results as $result) {
                $entity = $this->hydrator->hydrate($entityName,$result,$proxy);
                $this->identityMap->add($entity->result(),$entity->clone());
                $entities[] = $entity->result();
            }        
            return $entities;           
        }
        return [];
    }

    /**
     * seperate class name from class namespace
     * 
     * @param string $className
     * @throws \Exception
     */
    private function seperateClassName ($className) : string
    {
        $classNameComponents = explode("{$this->namespace}\\",$className);

        if (count($classNameComponents) !== 2) {
            throw new \Exception("Please specify the full qualified class name to get the related entity data"); //... class name given without the namespace
        }

        return $classNameComponents[1];
    }

    /**
     * extract data from the entity and create the array of column - value and column - placeholders ('?')
     */
    private function extractData (array $entityAttributes, mixed $entity, $proxy) : array
    {
        $data = [];
        $writable = [];
        $placeholders = [];        
        foreach ($entityAttributes as $name => $entityData) {
           $data[$entityData->getColumnName()] =  $proxy->getValue($name,$entity);
        }
        return $data;
    }
 
    /**
     * save an entity into the database
     *
     * @param object $entity
     * @return void
     */
    public function insert ($entity) : void
    {        
        $entityClass = $this->seperateClassName(\get_class($entity));
        $entityData = $this->entityConfig->get($entityClass);
        $proxy = $this->identityMap->getProxy('');
        $extractedData = $this->extractData($entityData->getAttributes(),$entity,$proxy);
        $this->writeManager->insert($entityClass,$extractedData);
    }

    /**
     * update entity in database
     * 
     * @param $entity
     */
    public function update ($entity) : void
    {
        $entityClass = $this->seperateClassName(\get_class($entity));
        $entityData = $this->entityConfig->get($entityClass);
        $proxy = $this->identityMap->getProxy('');        
        $extractedData = $this->extractData($entityData->getAttributes(),$entity,$proxy);
        $this->writeManager->update($entityClass,$extractedData);
    }

    /**
     * delete entity from the database
     * 
     * @param object $entity
     */
    public function delete ($entity) : void
    {
        $entityClass = $this->seperateClassName(\get_class($entity));
        $entityData = $this->entityConfig->get($entityClass);
        $proxy = $this->identityMap->getProxy('');        
        $extractedData = $this->extractData($entityData->getAttributes(),$entity,$proxy);
        $this->writeManager->insert($entityClass,$extractedData);
    }

    
}