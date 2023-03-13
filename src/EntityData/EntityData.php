<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Config;

use Infinite\SimpleOrm\Config\Attribute;

class EntityData {

    /**
     * name of corresponding database table
     *
     * @var String
     */
    private $tableName;

    /**
     * default repository class name
     *
     * @var string
     */
    private $DEFAULT_REPOSITORY_NAME = '';

    /**
     * name of repository class
     *
     * @var string
     */
    private $repositoryName;

    /**
     * Entity's attribute names corresponding
     * to primary keys of database tables
     *
     * @var array
     */
    private $primary = [];

    /**
     * Undocumented variable
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Read only properties
     *
     * @var array
     */
    private $readOnlyProps = ['autoIncrement'];

    /**
     * associated entities and association data
     *
     * @var array
     */
    private $associations = [];

    /**
     * associated entity names that need to be fetched eagerly
     *
     * @var array
     */
    private $eager_assoc = [];

    /**
     * namespace of the entity classes
     *
     * @var string
     */
    private $namespace;

    /**
     * class name of the entity
     *
     * @var string
     */
    private $className;

    public function __construct ($config,string $namespace, string $className)
    {        
        $this->namespace = $namespace;
        $this->className = $className;
        $this->parseConfiguration($config);
    }

    /**
     * parse the configuration object and extract values
     *
     * @param object $config
     * @return void
     * @throws \Exception
     */
    private function parseConfiguration (object $config) : void
    {

        if (!isset($config->table)) {
            throw new \Exception ("Table name is not defined for entity {$this->className} in entity definition");
        }
        $this->setTableName($config->table);
        $this->setRepositoryName($config);        

        if (!isset($config->attributes)) {
            throw new \Exception("Attributes are not defined in entity definition");
        }
        $this->setAttributes($config->attributes);       

        //... set associations
        if (isset($config->associations)) {
            $this->setAssociations($config->associations);            
        }
    }
    
    /**
     * set attribute
     *
     * @param array $attributes
     * @return void
     */
    private function setAttributes (array $attributes) : void
    {
        foreach ($attributes as $key => $val) {
            //... detailed description of attribute            
            if (is_array($val) && isset($val['primary']) && $val['primary'] === true) {
                $this->setPrimary($key);
            }            
            //... set attribute       
            $this->attributes[$key] = new Attribute($key,$val);
        }        
    }

    /**
     * Set the value of associations
     *
     * @param array $associations
     * @return  self
     */ 
    private function setAssociations(array $associations) : self 
    {
        foreach ($associations as $association => $data) {
            $this->associations[$association] = $data;
            if(isset($data['fetch']) && $data['fetch'] === 'eager') {
                //... add association name to fetch eager array
                $this->eager_assoc[] = $association;
            }            
        }        
        return $this;
    }
    
    /**
     * Get the value of tableName
     */ 
    public function getTableName() : string
    {
        return $this->tableName;
    } 
    
    /**
     * set the table name
     *
     * @param string $tableName
     * @return void
     */
    private function setTableName (string $tableName) : void
    {        
        $this->tableName = $tableName;
    }

    /**
     * set the repositroiy name
     *      *
     * @param string $name
     * @return void
     */
    private function setRepositoryName (object $config) : void
    {
        $this->repositoryName = isset($config->repository) ? $config->repository : $this->DEFAULT_REPOSITORY_NAME;
    }

    /**
     * Get the value of primary
     */ 
    public function getPrimary() : array
    {
        return $this->primary;
    }

    /**
     * Set the value of primary
     *
     * @return  self
     */ 
    private function setPrimary($primary) : self
    {
        array_push($this->primary,$primary);
        return $this;
    }    

    /**
     * Get the value of associations
     */ 
    public function getAssociations() : array
    {
        return $this->associations;
    }

    /**
     * get array of entity names that need to be fetched eagerly
     *
     * @return array
     */
    public function getEagerAssoc () : array
    {
        return $this->eager_assoc;
    }   

    /**
     * get the column name of an attribute
     *
     * @param string $attribute
     * @return string
     */
    public function column(string $attribute) : string
    {
        return $this->attributes[$attribute]->getColumnName() ?? null;
    }

    /**
     * get attribute names mapped to column names
     * 
     * @return array
     */
    public function getColumnNames () : array
    {
        $columns = [];
        foreach ($this->attributes as $name => $attribute) {
            $columns[$name] = $attribute->getColumnName();
        }
        return $columns;
    }

    /**
     * Get all of attributes as an array
     * 
     * @return array - [Attribute]
     */ 
    public function getAttributes() : array
    {        
        return $this->attributes;
    }    
    
    /**
     * get fully qualified class name of entity
     *
     * @return string
     */
    public function getFqcn () : string
    {
        return "{$this->namespace}\\{$this->classname}";
    }

    /**
     * get the class name of repository
     *
     * @return string
     */
    public function getRepositoryName () : string
    {
        return $this->repositoryName;
    }
   
}
?>