<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Mapper;

use Infinite\SimpleOrm\Config\EntityConfiguration;
use Infinite\SimpleOrm\Config\EntityData;
use Infinite\SimpleOrm\Query\QueryBuilder;
use Infinite\SimpleOrm\Query\QueryBuilderHelper;
use Infinite\SimpleOrm\Query\QueryDb;


class ReadManager {

    /**
     * Entity Configuration
     *
     * @var EntityConfiguration
     */
    private $entityConfig;

    /**
     * Query Builder
     *
     * @var [type]
     */
    private $queryBuilder;

    /**
     * Parameters to bind to the query
     *
     * @var array
     */
    private $queryParameters = [];

    /**
     * Entity Data of main entity
     *
     * @var EntityData
     */
    private $mainEntityConfig;

    /**
     * database abstraction layer
     * performs queries and transactions, fetches results
     *
     * @var QueryDb
     */
    private $queryDb;    
    
    /**
     * constructor
     *
     * @param EntityConfiguration $entityConfiguration
     * @param QueryBuilder $queryBuilder
     */
    public function __construct (EntityConfiguration $entityConfiguration, QueryBuilder $queryBuilder, QueryDb $queryDb)
    {
        $this->entityConfig = $entityConfiguration;
        $this->queryBuilder = $queryBuilder;
        $this->queryDb = $queryDb;    
    }

    private function joinAssociated (array $eagerAssoc) : void
    {        
        $targetEntity = $this->entityConfig->get($eagerAssoc['target']);
        $targetEntityTable = $targetEntity->getTableName();
        $targetEntityAttribute = "{$targetEntityTable}.{$targetEntity->column($eagerAssoc['refer'])}";
        $mainEntityInverse = "{$this->mainEntityConfig->getTableName()}.{$this->mainEntityConfig->column($eagerAssoc['inverse'])}";
        $this->queryBuilder->leftJoin($targetEntityTable,[$targetEntityAttribute, '=', $mainEntityInverse]);
        $this->queryBuilder->select(array_values($targetEntity->getColumnNames()));        
    }
    
    /**
     * build the where condition and return the conditions and values array
     *
     * @param array $conditions
     * @return void
     * @throws \Exception
     */
    private function filter (array $conditions) : void
    {
        $filter = [];
        foreach ($conditions as $condition => $value) {
            //... check if condition is available in entity's table columns
            $column = $this->mainEntityConfig->column($condition);
            if ($column) {
                $filter[$condition] = $value;
                $this->queryBuilder->where(["{$this->mainEntityConfig->getTableName()}.{$column}" => '?']);    //...check, don't this call here        
            }
            elseif (0) {
                //... if condition attribute is not found in main entity's main attributes, check  in associated entities
            }
            else {
                throw new \Exception(''); //... No matching attribute found
            }                       
        }
        $this->queryParameters = $filter;      
    }
    
    /**
     * query the database and fetch results
     *
     * @return array
     */
    private function query () : array
    {
        $this->queryDb->execute($this->queryBuilder->sql(),array_values($this->queryParameters));
        return $this->queryDb->fetchData();
    }

    /**
     * fetch an entity from database
     *
     * @param string $name - entity name
     * @param array $conditions - filtering conditions
     * @return mixed
     */
    public function getEntity (string $name, array $conditions = []) : array
    {
        $this->mainEntityConfig = $this->entityConfig->get($name);
        //... select entity's own attributes
        $this->queryBuilder->from($this->mainEntityConfig->getTableName())->select(array_values($this->mainEntityConfig->getColumnNames()));

        //... Has associations to fetch eager?
        $eagerAssoc = $this->mainEntityConfig->getEagerAssoc();
        if (count($eagerAssoc) > 0 ) {
            //... if yes join them
            $associations = $this->mainEntityConfig->getAssociations();
            foreach ($eagerAssoc as $assoc)  {
                $this->joinAssociated($associations[$assoc]);
            }            
        }

        if (count($conditions) > 0) {
            //... if condition array is passed, build query to filter data using condition
            $this->filter($conditions);            
        }

        return $this->query();
    }

}

?>