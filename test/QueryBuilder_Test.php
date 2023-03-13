<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/

declare(strict_types = 1);

use Infinite\SimpleOrm\Query\QueryBuilder;

require_once('../src/Query/QueryBuilder/QueryBuilder.php');

/**
 * Test following things
 * 
 * select
 * select - with where
 * select - where with multiple conditions
 * select with left join
 * select with left join with multiple conditions
 * select with left join with where
 * 
 * insert
 * 
 * update - with where
 * update with where - multiple conditions
 * 
 * delete
 * delete with multiple conditions
 */

$queryBuilder = new QueryBuilder();

$selectColumns = ['id', 'name', 'location'];

echo "============== SELECT ================ \n";

$queryBuilder->from('users')->select($selectColumns);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============== SELECT WITH WHERE  =================== \n";

$queryBuilder->from('users')->select($selectColumns)->where(['id' => '123','name' => "'abc'"]); //... no table name for where condition
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============== SELECT WITH WHERE - WITH  MULTIPLE CONDITIONS ============ \n";

$queryBuilder->from('users')->select($selectColumns)->where([['id','=','00002'],['name','=',"'aaaa'"]]);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============== SELECT WITH LEFT JOIN \n";

$queryBuilder->from('users')->select($selectColumns)->leftJoin('products',['product.user_id','=','users.id'])->select(['product_id','product_name']);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============== SELECT WITH LEFT JOIN WITH MULTIPLE CONDITIONS \n";

$queryBuilder->from('users')->select($selectColumns)->leftJoin('products',[['product.user_id', '=', 'users.id'],['product.type','=','electronic']]);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============== SELECT LEFT JOIN WITH WHERE \n";

$queryBuilder->from('users')->select($selectColumns)->leftJoin('products',[['product.user_id', '=', 'users.id'],['product.type','=','electronic']])->where(['user.id', '=', '123']);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();


echo "============ INSERT ======================== \n";

$queryBuilder->insert('users',['id' => '?','name' => '?','location' => '?']);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============== UPDATE - WITH WHERE ================ \n";

$queryBuilder->update('users',['name' => 'changed_name'],['users.id','=','123']);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============== UPDATE - WITH WHERE - MULTIPLE CONDITIONS ================ \n";

$queryBuilder->update('users',['name' => 'changed_name'],[['users.id','=','123'],['users.name','=',"'abc'"]]);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();


echo "============== DELETE ================ \n";

$queryBuilder->delete('users',['users.id','=','123']);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();

echo "============ DELETE - WITH MULTIPLE CONDITIONS ======================== \n";

$queryBuilder->delete('users',[['users.id','=','123'],['users.name','=',"'abc'"]]);
echo $queryBuilder->sql();
echo "\n \n";
$queryBuilder->reset();
