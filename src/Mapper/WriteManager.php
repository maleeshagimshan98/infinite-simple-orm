<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Mapper;

use Infinite\SimpleOrm\Config\EntityConfiguration;
use Infinite\SimpleOrm\Query\QueryBuilder;
use Infinite\SimpleOrm\Query\QueryDb;

class WriteManager {

    /**
     * Query Builder
     *
     * @var QueryBuilder
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
    private $entityConfig;

    /**
     * store all the sql queries to execute in a single transaction
     *
     * @var array
     */
    private $sqlStatementStack = [];

    public function __construct (EntityConfiguration $entityConfiguration, QueryBuilder $queryBuilder, QueryDb $queryDb)
    {
        $this->queryBuilder = $queryBuilder;  
        $this->queryDb = $queryDb;
        $this->entityConfig = $entityConfiguration;
    }

    private function generateQueryParameters (array $attributes, array $values) : array
    {
        $placeholders = [];
        $insertValues = [];
        //... make sure to check readOnlyProps/ autoIncrement

        foreach ($values as $name => $value) {
            if ($attributes[$name]->isWritable()) {
                $placeholders[$attributes[$name]->getColumnName()] = "?";
                $insertValues[] = $value;
            }
        }
    /**
        foreach ($attributes as $name => $attribute) {
            if ($attribute->isWritable()) {
                $placeholders[$attribute->getColumnName()] = "?";
                $insertValues[] = $values[$name];
            }            
        } */
        return ["placeholders" => $placeholders, "values" => $insertValues];
    }

    /**
     * create insert statement, add it to sql statement stack
     */
    public function insert (string $name, $values ) : void
    {
        $entityConfig = $this->entityConfig->get($name);
        $queryParameters = $this->generateQueryParameters($entityConfig->getAttributes(),$values);
        
        /**
         * Do not pass actual values to below function, pass '?' instead
         */
        $this->queryBuilder->insert($entityConfig->getTableName(),$queryParameters['placeholders']);
        $this->sqlStatementStack[] = ["query" => $this->queryBuilder->sql(),"values" => $queryParameters['values'] ];
    }

    /**
     * create update statement, add it to sql statement stack
     */
    public function update (string $name, array $values, array $condition = []) : void
    {
        $entityConfig = $this->entityConfig->get($name);        
        //... get array of column names for values
        $queryParameters = $this->generateQueryParameters($entityConfig->getAttributes(),$values);        
        //... get array of values for conditions
        $conditionParameters = $this->generateQueryParameters($entityConfig->getAttributes(),$condition);
        $this->queryBuilder->update($entityConfig->getTableName($name),$queryParameters['placeholders'],$conditionParameters["placeholders"]);
       //echo  $this->queryBuilder->sql(); die;
        $this->sqlStatementStack[] = ["query" => $this->queryBuilder->sql(), "values" => array_merge($queryParameters["values"],$conditionParameters["values"])];


    }

    public function delete (string $name, array $condition = []) : void 
    {
        $entityConfig = $this->entityConfig->get($name);                
         //... get array of values for conditions
        $conditionParameters = $this->generateQueryParameters($entityConfig->getAttributes(),$condition);
        $this->queryBuilder->delete($entityConfig->getTableName(),$conditionParameters["placeholders"]);
        $this->sqlStatementStack[] = ["query" => $this->queryBuilder->sql(), "values" => $conditionParameters["values"]];
    }

    public function write () : bool
    {
        //... this is the main function that is being called when writing to database
        //... writes all the queries to the database in a single transaction
        try {
            $this->queryDb->transaction();
            foreach ($this->sqlStatementStack as $sqlStataement) {
                $this->queryDb->execute($sqlStataement["query"],$sqlStataement["values"]);
            }
            $this->queryDb->commit();
            return true;
        }
        catch (\Exception $e) {
            $this->queryDb->rollback();
            throw new \Exception("Database transaction failed");
        }
    }
    


}

?>