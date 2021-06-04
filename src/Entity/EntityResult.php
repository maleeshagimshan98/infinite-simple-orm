<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * Entity's result object
 */

 namespace Infinite\DataMapper\Entity;
 /**
  * Entity's result object class
  */
 class EntityResult
 {
    /**
     * timestamp of the creation of this instance
     *
     * @var integer
     */
    private $__createdAt;

   /**
    * name of entity of this object belong
    *
    * @var string
    */
   protected $entityName;

   /**
    * entity attributes
    *
    * @var array
    */
  protected $props = [];

   /**
    * constructor

    * @param array $props properties of entity
    */
     public function __construct ($props,$name)
     {
         $this->__createdAt = explode(".",microtime(true))[1]; 
         $this->entityName = $name;
         if ($this->is_assoc($props))
         {
            foreach ($props as $key => $value)
            {
                $this->props[] = $key;
                $this->$key = $value;
            }
         }
         else 
         {
             foreach ($props as $key)
             {
                $this->props[] = $key;
                 $this->$key = "";
             }
         }
         
     }

   final public function __getCreatedTime ()
   {
      return $this->__createdAt;
   }

   /**
    * return entity name of this object belongs
    *
    * @return string $entityName entity name
    */
   public function name ()
   {  
      return $this->entityName;
   }

     /**
      * check if an array is an associative array or not
      *
      * @param array $array
      * @return boolean
      */
    protected function is_assoc ($array)
    {
        foreach (array_keys($array) as $k => $v)
        {
          if ($k !== $v)
          {
            return true;
          }
        }
        return false;
      }

      /**
      * __call magic method - use to
      * dynamically create set and get methods
      *
      * @return mixed
      */
     public function __call ($name,$args)
     {         
         $functionName = strtolower($name);
         $isGetter = explode("get",$functionName);
         $isSetter = explode("set",$functionName);

         if (is_array($isGetter) && count($isGetter) > 1)
         {
             if ($this->props($isGetter[1]))
             {
                 return $this->$isGetter[1];
             }
             
         }

         if (\is_array($isSetter) && count($isSetter) > 1)
         {
            if ($this->props($isSetter[1]))
            {
                $this->set($isSetter[1],$args);
            }
         }

     }

     /**
      * clone this instance, to keep track of changes
      * clones properties even if it is an object
      *
      * @return void
      */
     public function __clone () {
        
        foreach ($this->props as $prop) {
           if (\is_object($this->$prop)) {
              $this->$prop = clone $this->$prop;
           }
        }
     }

     /**
     * get instance's property names as an associative array
     * 
     * @param object $data - object with data
     * @return array
     */
    public function getAssocArray()
    {
      $arr = [];
      foreach($this->props as $key)
      {
          if (isset($this->$key))
          {
              $arr[$key] = $this->$key;
          }
      }//echo json_encode($arr);
      return $arr;
   }

   /**
    * get entity result object's properties in an indexed array
    *
    * @return array
    */
   public function getPropsArray ()
   {
      $arr = [];
      foreach ($this->props as $key)
      {
         $arr[] = $this->$key;
      }
      return $arr;
   }


    /**
    * get a single attribute
    *
    * @param string $attrib
    * @return string attribute
    * @throws \Exception
     */
    protected function props ($prop = null) 
    {
      if (empty($prop)) {
         return $this->props;
      }

      if (!isset($this->$prop)) {
         throw new \Exception("Undefined_Property");
      }
      return $this->$prop;
    }

    /**
     * get entity's attributes
     *
     * @param mixed $attribute
     * @return string|array
     * @throws \Exception
     */
    public function get ($prop = null)
    {
       try
       {
         if (is_array($prop))
         {
            $arr = [];
            foreach ($prop as $key) {                         
               $arr[] = $this->props($key);
            }
            return $arr;
         }
         return $this->props($prop);
       }
      catch (\Exception $e)
      {
         if ($e->getMessage() == "Undefined_Property" ) //IMPORTANT - CONSIDER ADDING && $prop !== null
         {
            return $this->props();
         }
      }       
   }

    /**
     * set a property
     *
     * @param string $name property name
     * @param mixed $prop property value
     * @return void
     */
    public function set ($name,$prop)
    { 
       try {
          $this->props($name);
          $this->$name = $prop;
       }
       catch (\Exception $e) {
          if ($e->getMessage() === "Undefined_Property") {
             return;
          }
       }         
    }

    /**
     * set object proprties from an associative array
     *
     * @param array $array an associative array of object props and values
     * @return void
     */
    public function __setFromObj ($obj)
    {
       foreach ($this->props() as $key)
       {
          if (!isset($obj->$key))
          {
             continue; //CHECK
          }
          $this->$key = $obj->$key;
       }
    }
 }

 ?>