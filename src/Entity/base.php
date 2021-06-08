<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * base class for defining entities
 * in the database
 */

 namespace Infinite\DataMapper;

 use Infinite\DataMapper\Entity\AttributeMapContainer;
 use Infinite\DataMapper\Entity\AssociatedEntityContainer;
 use Infinite\DataMapper\Entity\EntityResult;

 /**
  * Entity class
  * represents an entity
  */
 class Entity {
    protected $qb;

    /**
     * entity name
     *
     * @var string
     */
    public $entityName;

    /**
     * entity's attributes
     *
     * @var Infinite\DataMapper\Entity\AttributeContainer
     */
    public $attribs;

    /**
     * entity's property names
     *
     * @var Infinite\DataMapper\Entity\EntityResult object
     */
    protected $entityResult;

    /**
     * entity table's name
     *
     * @var string
     */
    protected $entityTable = "";

    /**
     * entity associations with other entities
     *
     * @var Infinite\DataMapper\Entity\AssociatedEntityContainer
     */
    public $associations;
    /**
     * entity's data limit - for pagination
     *
     * @var integer
     */
    protected $limit = 10;

    /**
     * constructor
     * 
     * @param string $name database table name of the entity    
     * @param array|object $attribs
     * @return void
     * @throws \Exceptions
     */
    public function __construct ($attribs,$name = "")
    {
       if (!$attribs) {
          throw new \Exception("Invalid_Entity_Definition");
      }
       $this->attribs = new AttributeMapContainer();
       $this->associations = new AssociatedEntityContainer();
       $this->entityName = $name;
       $this->init($attribs,$name);
    }

    /**
     * get entity table
     *
     * @return string
     */
    public function table () : string
    {
      return $this->entityTable;
    }

    /**
     * get entity's name
     *
     * @return string
     */
    public function name () 
    {
       return $this->entityName;
    }

   /**
    * get primary key
    *
    * @return string|array
    */
   public function primary ()
   { 
      return $this->attribs->primary();
   }

    /**
     * validate data with defined data types
     * 
     * @param  mixed - data
     * @return boolean
     */
    public function validate ($props)
    {

    }

    /**
     * set entity's table name
     *
     * @param object $attribs entity definition object
     * @param string $name entity name
     * @return void
     * @throws \Exception
     */
    protected function setEntityTableName ($attribs,$name)
    {
       if(!isset($attribs->__table_name)) {
               $this->tableName = $name;
       }
       if (isset($attribs->__table_name) && ($attribs->__table_name !== "")) {
               $this->entityTable = $attribs->__table_name;
       }
       else {
          $this->entityTable = $name;
       }
    }

    /**
     * map entity attributes from configuration
     *
     * @param object $attribs entity attributes
     * @param string $name entity table name
     * @return AttributeMapContainer attribute map container
     */
    protected function init ($attribs,$name) : AttributeMapContainer
    { /* TESTING */   //echo \json_encode($attribs);
       $keys = [];
       
      $this->setEntityTableName($attribs,$name);
      $this->attribs->init($attribs);

      if (isset($attribs->_assoc)) {
         $this->associations->set($attribs->_assoc);
      }
       
       //$this->initEntityResult();       
       return $this->attribs;
      }
      
      /**
       * initialize EntityResult object
       *
       * @return void
       */
      protected function initEntityResult ()
      {  
         $keys = $this->attribs->getAttribs();
         $keys = array_merge($keys,$this->associations->getAssociationKeys());
         return new EntityResult($keys,$this->entityName);   //always return a new instance      
      }

      /**
       * get entity result object
       *
       * @return Infinite\DataMapper\Entity\EntityResult object
       */
      public function getEntityResult ()
      {
         return $this->initEntityResult();
      }

    /**
     * format column names with table name
     *
     * @param string|array $columns
     * @return mixed
     */
    protected function withTable ($columns)
    {
       if (is_string($columns))
       {
          return $this->entityTable.".".$columns;
       }

       if (is_array($columns))
       {
          $formattedColumns = [];
          foreach ($columns as $key)
          {
             $formattedColumns[] = $this->entityTable.".".$key;
          }
          return $formattedColumns;
       }
    }

    /**
     * get entity attributes' column names (any or all)
     *     
     * @param string|array $attributeName
     * @return string|array
     */
    public function mapAttribsToColumn ($attributeName = null)
    {
       if (\is_null($attributeName))
       {
          return $this->withTable($this->attribs->getMapped());
       }      
       
       if (is_array($attributeName))
       {
         $columns = [];
         foreach ($attributeName as $val)
         {
            $columns[] = $this->withTable($this->attribs->getMapped($val));
         }
         return $columns;
       }

       return $this->withTable($this->attribs->getMapped($attributeName)); /* $attributeName === String */
    }

    /**
     * map properties of database result (column names) to entity attribute names
     *
     * @param mixed $data
     * @param EntityResult $entityResult
     * @return EntityResult entity result object
     */
    protected function mapColumnsToAttribs ($data,$entityResult)
    {       
       $inverseMap = $this->attribs->getInverseMap();
       
       foreach ($inverseMap as $key => $val) {
          $entityResult->$val = $data->$key;                              
       }
       return $entityResult;
    }

    /**
     * hydrate entity with the data fetched from database
     *
     * @param array|object $data  fetched data from database
     * @return self
     */
    public function hydrate ($data)
    { 
       $data = (object) $data;
       $arr = [];
       $entityResult = $this->initEntityResult();  //always returns a new instance
       return $this->mapColumnsToAttribs($data,$entityResult);
    }   

    /**
     * get an indexed array of data for insertion, based on entity's atributes
     * (only entity owned attributes)
     * returns an indexed array containing values
     *
     * @param [type] $data
     * @return array
     */
    public function insertProps ($entity)
    {
       $arr = [];

       $attribs = $this->attribs->getWritableAttribsMapped();
       
       foreach ($attribs as $key)
       {
          $attrib = $this->attribs->getAttribs($key);
          $arr[] = $entity->$attrib;
       }
       return $arr;
    }
 }
?>