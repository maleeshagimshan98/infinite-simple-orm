<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

require_once('../src/DbConnection/DbConnection.php');
require_once('../src/Query/QueryDb.php');
require_once('../src/Query/QueryBuilder/QueryBuilder.php');
require_once('../src/EntityData/EntityConfiguration.php');
include_once('../src/EntityData/EntityData.php');
include_once('../src/EntityData/Attribute.php');
require_once('../src/Mapper/ReadManager.php');

/**
 * do the following tests
 * 
 * get an entity
 */

 $entityConfigPath = '../config/entity_definition.yaml';
 $entityNamespace = 'App\Entities';

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


$readManager = new Infinite\SimpleOrm\Mapper\ReadManager($entityConfiguration,$queryBuilder,$queryDb);

$result = $readManager->getEntity('Subject');

var_dump($result);




 