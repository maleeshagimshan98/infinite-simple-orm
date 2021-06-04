<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * Entity Container Class
 */

 namespace Infinite\DataMapper;
 /**
  * Entity container
  */
 class EntityContainer 
 {

    /**
     * full qualified class name for the entity
     *
     * @var string
     */
    protected $base_class_name = "\Infinite\DataMapper\Entity\\";

    /**
     * database schema
     *
     * @var object
     */
    protected $dbSchema;

    /**
     * entities - [...,\Infinite\DataMapper\Entity\ENTITY_CLASS_NAME]
     *
     * @var array
     */
    protected $entities = [];


     /**
      * constructor
      */
     public function __construct ($entityDefinition = null)
     {
         if(!empty($entityDefinition))
         {
             $this->parseEntityDefinition($entityDefinition);
             //$this->init();
         }
     }

     /**
      * initialize entities
      *
      * @param string $defName
      * @param string $class
      * @param string $name
      * @return void
      */
     public function init () 
     {        
        foreach ($this->dbSchema as $key => $value)
        {  
            $entityClass = $this->base_class_name."$key";
            $this->entities[$key] = new $entityClass($value,$key);
        }
     }

     /**
      * create an entity on the go
      *
      * @param string $name entity name
      * @return \Infinite\DataMapper\Entity entity object
      */
     public function entity ($name)
     {
         $entityDef = $this->getDefinition($name);
         if (!$entityDef)
         {
             throw new \Exception("Undefined_Entity");
         }
         $entityClass = $this->base_class_name."$name";
         return $this->entities[$name] = new $entityClass($entityDef,$name);         
     }

     /**
     * parse database entity definition
     *
     * @param [type] $schema JSON object defining the db schema
     * @return void
     */
    protected function parseEntityDefinition ($schema)
    {
        $arr = [];
        foreach ($schema as $key => $val) {
            $arr[$key] = $val;
        }
        $this->dbSchema = (object) $arr;
    }

    /**
     * get entity definitions
     *
     * @param string $name
     * @return mixed
     */
    protected function getDefinition ($name)
    { //echo json_encode($name);
        return $this->dbSchema->$name ?? false;
    }

    /**
     * checks if current entities count is zero
     *
     * @return boolean
     */
    public function is_empty () 
    {
        return count($this->entities) == 0;
    }


     /**
      * Get an entity
      *
      * @param string $entity Entity name
      * @return \Infinite\DataMapper\Entity object
      * @throws \Exception
      */
     public function get ($entity)
     {
        if (empty($this->entities[$entity])) {

            return $this->entity($entity);
            //throw new \Exception("Undefined_Entity");
        }
        return $this->entities[$entity];
     }

     /**
      * get all entities
      *
      * @return array
      */
     public function getAll () 
     {
         return $this->entities;
     }

     /**
      * set entity
      *
      * @param string $name
      * @param Infinite\DataMapper\Entity $entity entity object
      * @return void
      */
     public function set ($name,$entity)
     {
         $this->entities[$name] = $entity;                    
     }

     /**
      * clear entities
      *
      * @return void
      */
     public function clear ()
     {
         $this->entities = [];
     }

     /**
      * TESTING PURPOSE
      *
      * @return void
      */
     public function test_json ()
     {
         return json_encode($this);
     }
 }

 ?>