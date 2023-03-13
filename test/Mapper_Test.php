<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

use App\Entities\Student;
use Infinite\SimpleOrm\IdentityMap;
use Infinite\SimpleOrm\Mapper;

require_once('../src/DbConnection/DbConnection.php');
require_once('../src/Query/QueryDb.php');
require_once('../src/Query/QueryBuilder/QueryBuilder.php');
require_once('../src/EntityData/EntityConfiguration.php');
include_once('../src/EntityData/EntityData.php');
include_once('../src/EntityData/Attribute.php');
require_once('../src/Mapper/WriteManager.php');
require_once('../src/Mapper/ReadManager.php');
require_once('../src/Mapper/Mapper.php');
include_once('../src/IdentityMap/IdentityMap.php');
include_once('../test/Test_Entities/Student.php');

/**
 * test following things
 * 
 * get
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
$identityMap = new IdentityMap();

$mapper = new Mapper($entityConfiguration,$queryBuilder,$identityMap);

$student = new Student('00110011','Laurel',23,'USA');

echo "========= GET ============== \n";

$newStudent = $mapper->get('Student',['id' => '1101101',]);
//$mapper->write();
//var_dump($mapper);

echo "=================================== \n \n";

echo "========= INSERT ============== \n";

$mapper->insert($student);
//$mapper->write();
//var_dump($mapper);

echo "=================================== \n \n";


echo "============ UPDATE ================ \n";
$mapper->update($newStudent);
//var_dump($mapper);
//$mapper->write();
echo "=================================== \n \n";
//die;

echo "========= DELETE ============== \n";
$mapper->delete($newStudent);
//var_dump($mapper);
echo "=================================== \n \n";

echo "========= WRITE - commit a trasaction ============== \n";
var_dump($mapper);
//var_dump($mapper);
echo "=================================== \n \n";

