<?php 

include_once __DIR__."/pdo_connection.php";
include_once __DIR__."/vendor/autoload.php";
include_once __DIR__."/Products.php";

use Infinite\DataMapper\EntityManager;
use Infinite\DataMapper\Entity\Product;
//use Infinite\DataMapper\Entity\sku;


$connection = new pdoConnection();

$entityManager = new EntityManager ((object) [
    "connection" => $connection->connection()
]);

/** GET PRODUCTS */

//$product = $entityManager->get("product",["id","llmp_001"])->go('llmp_001');

//$product = $entityManager->get("product")->go();

//$product = $entityManager->get("product")->associate("product_sku")->go();
//$product1 = $product[0];
//$product1->product_name = "llmp_test_0002";
//$entityManager->save($product1)->go();
//$entityManager->save($product1);
echo json_encode($product);

/* -------- */

/** INSERT PRODUCTS */
/*
$product = $entityManager->entity("product");
$props = ["id" => "llmp_010", "product_name" => "test_product", "img_url" => ""];
$entityManager->save($product,$props);
$entityManager->go();*/
//echo json_encode($product);

/**----------------------- */

?>