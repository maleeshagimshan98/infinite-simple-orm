<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * product domain model in the database
 */

 namespace Infinite\DataMapper\Entity;

 include_once __DIR__."/vendor/autoload.php";

 use Infinite\DataMapper\Entity;

 class product extends Entity {
     /**
      * entity name
      *
      * @var string
      */
     public $entityName = "product";
     
     protected $entityTable = "product";


    public function __construct ($attribs,$name) {
        parent::__construct($attribs,$name);
    }

    public function getProduct () {
        return;
    }

    
 }
?>