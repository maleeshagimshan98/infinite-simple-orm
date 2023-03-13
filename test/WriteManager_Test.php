<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

use Infinite\SimpleOrm\Mapper\WriteManager;

require_once('../src/DbConnection/DbConnection.php');
require_once('../src/Query/QueryDb.php');
require_once('../src/Query/QueryBuilder/QueryBuilder.php');
require_once('../src/EntityData/EntityConfiguration.php');
include_once('../src/EntityData/EntityData.php');
include_once('../src/EntityData/Attribute.php');
require_once('../src/Mapper/WriteManager.php');

/**
 * test following things
 * 
 * insert
 * update
 * delete
 * write (commit all queries in a single transaction)
 */

$entityConfigPath = '../config/entity_definition.yaml';
$entityNamespace = 'App\\Entities';

$dbConn = Infinite\SimpleOrm\DbConnection::getConnection([
   'host' => 'localhost', 
   'driver' => 'mysql',
   'username' => 'root',
   'password' => '',
   'db' => 'infinite_orm'
]);

$queryDb = new Infinite\SimpleOrm\Query\QueryDb($dbConn);
$queryBuilder = new Infinite\SimpleOrm\Query\QueryBuilder();
$entityConfiguration = new Infinite\SimpleOrm\Config\EntityConfiguration($entityConfigPath,$entityNamespace);

$writeManager = new WriteManager($entityConfiguration,$queryBuilder,$queryDb);

echo "========= INSERT ============== \n";

$writeManager->insert('Student',['id' => '1101101', 'name' => 'Chen', 'age' => '23', 'country' => 'China']);
//$writeManager->write();
//var_dump($writeManager);

echo "=================================== \n \n";


echo "============ UPDATE ================ \n";
$writeManager->update('Student',['name' => 'Chen Cook', 'age' => '21', 'country' => 'China'],["id" => "1101101", "name" => "Chen"]);
//var_dump($writeManager);
//$writeManager->write();
echo "=================================== \n \n";
//die;

echo "========= DELETE ============== \n";
$writeManager->delete('Student',['id' => '1101101', 'name' => 'Chen Cook', 'age' => '21', 'country' => 'China']);
//var_dump($writeManager);
echo "=================================== \n \n";

echo "========= WRITE - commit a trasaction ============== \n";
var_dump($writeManager);
$writeManager->write();
//var_dump($writeManager);
echo "=================================== \n \n";

