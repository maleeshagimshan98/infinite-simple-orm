<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * Entity's Attribute Map Container Class
 */

 namespace Infinite\DataMapper\Entity;
 /**
  * Associated entity container
  */
 class AttributeMapContainer
 {

   /**
    * entity attributes map - [ 'attribute' => 'column_name']
    *
    * @var array
    */
   protected $attrib_map = [];

   /**
    * inverse of attribute mapping to column
    *
    * @var array
    */
   protected $attrib_map_inverse = [];

   /**
    * entity's primary keys
    *
    * @var array
    */
   protected $primary = [];

   /**
    * entity's properties (eg - autoIncrement)
    *
    * @var array
    */
   protected $properties = [];

   /**
    * read only properties in entity (eg - AUTO INCREMENTED values)
    *
    * @var array
    */
   protected $readOnlyProps = [
      "autoIncrement"
   ];

   /**
    * constructor
    */
     public function __construct ()
     {        
     }

    /**
    * get a single attribute_map element
    *
    * @param string $attrib attrib_map element name
    * @return string attribute_map element
    * @throws \Exception
     */
    protected function map ($attrib = null) 
    {      
      if (!isset($this->attrib_map[$attrib])) {
         throw new \Exception("Undefined_Attribute");
      }
      return $this->attrib_map[$attrib];
    }

    /**
     * get entity's primary keys
     *
     * @return array
     */
    public function primary () {
       return $this->primary;
    }

    /**
    * set primary key
    *
    * @param object $attrib
    * @return void
    */
   protected function setPrimaryKey ($key)
   {//echo \json_encode($key);
      if ($key->primary !== false)
      {
         $this->primary[] = $key->primary;
      }
   }

   /**
    * set entity's properties
    *
    * @param object $properties object containing properties of entity attribute
    * @return void
    */
   protected function setProperties ($properties)
    {
       if (isset($properties["autoIncrement"])) {
          $this->properties["autoIncrement"][] = $properties["autoIncrement"];
       }
    }

    /**
     * get entity attribute's properties
     *
     * @param string $name property name (eg - autoIncrement)
     * @return array
     * @throws \Exception
     */
    public function getProperties ($name)
    {
       if (!isset($this->properties[$name])) {
          throw new \Exception("Undefined_Property");
       }
       return $this->properties[$name];
    }

    /**
     * sets the attribute's name with respect of configuration options
     *
     * @param string $attribName attribute name entity
     * @param string|object $attrib attribute options (if any), or (similar as $attribName)
     * @return object
     */
    protected function parseAttribute ($attribName,$attrib)
    {
       $result = (object) ["name" => "", "primary" => false, "properties" => []];

       //if property is an association
       if ($attribName === "_assoc") {
         $result =  false;
         return $result;
      }

       if (\is_object($attrib))
       {          
          if (!empty($attrib->name))
          {
             $result->name = $attrib->name;
          }
          if (isset($attrib->primary) && $attrib->primary == true)
          { //attribs with primary key, but no name comes here            
             $result->primary = $attrib->name ?? $attribName;
             $result->name = $attrib->name ?? $attribName;
          }
          if (isset($attrib->autoIncrement) && $attrib->autoIncrement == true)
          {
            $result->properties["autoIncrement"] = $attrib->name ?? $attribName;
          }
       }
       elseif (is_string($attrib))
       {
          $result->name =  $attrib;
       }
       return $result;       
    }

    /**
     * set entity's attributes
     *
     * @param string|object $attrib
     * @return void
     */
    public function init ($attrib)
    {
      foreach ($attrib as $key => $values) {
         $attribute = $this->parseAttribute($key,$values);

         if ($attribute) {            
            //setting the attribute mapping to columns
            $this->set($key,$attribute->name);
            $this->setPrimaryKey($attribute);

            if (\count($attribute->properties) > 0) {
               $this->setProperties($attribute->properties);
            }         
         }
      }
    }

    /**
     * get writable attributes of entity, mapped to columns 
     *
     * @return array
     */
    public function getWritableAttribsMapped ()
    {
       try {
      $readOnly = [];
      foreach ($this->readOnlyProps as $key) {
         $readOnly = array_merge($readOnly,$this->getProperties($key));         
       }
       return array_diff($this->getMapped(),$readOnly);
      }
      catch (\Exception $e) {
         return $this->getMapped();
      }
    }

    /**
     * get column names of entity attributes
     *
     * @param mixed $attribute
     * @return string|array
     * @throws \Exception
     */
    public function getMapped ($attribute = null)
    {
       if (empty($attribute))
         {
            return array_values($this->attrib_map);
         } 
      if (is_array($attribute))
      {
         $arr = [];
         foreach ($attribute as $key) {                         
            $arr[] = $this->map($key);
         }
         return $arr;
      }
      return $this->map($attribute); /** if $attribute === String */            
   }

   /**
    * map entity attributes of column names
    *
    * @param string $attribute
    * @return string|array
    * @throws \Exception
    */
   protected function mapAttribs ($attribute)
   {
      if ($attribute === null) {
         return array_keys($this->attrib_map_inverse); //check
      }

      if (!is_null($attribute) && !isset($this->attrib_map_inverse[$attribute])) {
         throw new \Exception("Undefined_Attribute");         
      }

      else {
         $attrib;
         foreach ($this->attrib_map_inverse as $key => $val) { //PUT THIS LOGIC INTO THE COMMON CLASS
            if ($key === $attribute) {
               $attrib = $val;
               break;
            }
         }
         return $attrib;
      }
   }

   /**
    * get entity's attributes
    *
    * @param mixed $attribute attribute name/s
    * @return array
    */
   public function getAttribs ($attribute = null)
   {
      if (\is_array($attribute)) {
         $arr = [];
         foreach($attribute as $key) {
            $arr[] = $this->mapAttribs($key);            
         }
         return $arr;
      }
      return $this->mapAttribs($attribute);      
   }

    /**
     * set entity attributes and mapping column names
     *
     * @param string $name
     * @param string $attrib
     * @return void
     */
    public function set ($name,$attrib)
    {
       $this->attrib_map[$name] = $attrib;
       $this->setInverseMap($attrib,$name);         
    }

    /**
     * set attrb_map's inverse
     *
     * @param string $name
     * @param string $attrib
     * @return void
     */
    protected function setInverseMap ($name,$attrib) 
    {
       $this->attrib_map_inverse[$name] = $attrib;
    }

    /**
     * get attrib_map's inverse
     *
     * @return array
     */
    public function getInverseMap ()
    {
       return $this->attrib_map_inverse;
    }
 }

 ?>