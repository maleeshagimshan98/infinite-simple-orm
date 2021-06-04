<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * sku domain model in the database
 */

 namespace Infinite\DataMapper\Entity;

 use Infinite\DataMapper\Entity;

 include_once __DIR__."/EntityManager/Entity/base.php";

 /**
  * entity class for sku
  */
 class sku extends Entity {
     protected $entityTable = "sku";
     public $entityName = "sku";

     /**
      * constructor
      *
      * @param string $name database table name of the entity
      * @param array|object $attribs entity attributes
      */
     public function __construct ($attribs,$name) {
         parent::__construct($attribs,$name);
         
     }
 }