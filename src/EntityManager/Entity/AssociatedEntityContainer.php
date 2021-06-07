<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * Entity Container Class
 */

 namespace Infinite\DataMapper\Entity;
 /**
  * Associated entity container
  */
 class AssociatedEntityContainer 
 {

    /**
     * entity association data
     *
     * @var array
     */
    protected $associations = [];

    protected $associationKeys = [];

    /**
     * curretly associated entities on this entity
     *
     * @var array
     */
    protected $currentAssociated = [];

    /**
     * constructor
     */
     public function __construct ()
     {

     }

     /**
      * get an association
      *
      * @param string $association
      * @return string
      * @throws \Exception
      */
     public function get ($association = null)
     {
        if (empty($association))
        {
            return $this->associations;
        }     
         if (!isset($this->associations[$association]))
         {
            throw new \Exception("Undefined_Association");
         }  
         return $this->associations[$association];
     }

     /**
      * get associations key names
      *
      * @return array
      */
     public function getAssociationKeys () 
     {
        return $this->associationKeys;
     }

     /**
      * set an association
      *
      * @param object $data
      * @return void
      * @throws \Exception
      */
     public function set ($data)
     {        
        foreach ($data as $key => $value)
        {
           $this->associationKeys[] = $key;  // REMOVE THIS PROPERTY, USE SOMETHING SIMILAR TO AttributeMapContainer          
            if (empty($value->target))
            {
               throw new \Exception("Target_Entity_Undefined");
               return; //IMPORTANT CHECK WHAT HAPPENS IN CASE OF ERROR
            }            
            $this->associations[$key] = $value;
        }           
    }
 }

 ?>