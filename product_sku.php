<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * product_sku domain model in the database
 */

 namespace Infinite\DataMapper\Entity;

 include_once __DIR__."/vendor/autoload.php";

 use Infinite\DataMapper\Entity;

 /**
  * class for product_sku entity
  */
 class product_sku extends Entity {
    protected $entityTable = "product_sku";
    public $entityName = "product_sku";
    public $pricing_string = "";

    /**
      * constructor
      *
      * @param string $name database table name of the entity
      * @param array|object $attribs entity attributes
      */
    public function __construct ($attribs,$name) {
        parent::__construct($attribs,$name);
        $this->entityName = "product_sku";
    }

    public function getProductSku () {
        return;
    }

    
 }
?>