<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

require_once('../test/Test_Entities/Student.php');
require_once('../src/Mapper/Proxy.php');


/**
 * do the following tests
 * 
 * get a value
 * set a value
 */

 $proxy = new Infinite\SimpleOrm\Mapper\Proxy();

 $entity = new App\Entities\Student(121223,'thea','19','usa');
 $reflectionClass = new ReflectionClass('App\Entities\Student');

 $proxy->setReflectionClass($reflectionClass);

 echo "=========== GETTING A VALUE =============== \n \n";
 $value = $proxy->getValue('name',$entity);
 echo "value of name is {$value} \n \n ";

 echo "=========================================== \n \n \n";

 echo "=========== GETTING A VALUE (INVALID PROPERTY) =============== \n \n";
 try {
    $value = $proxy->getValue('subject',$entity);
 }
 catch (Exception $e) {
     echo $e->getMessage() . "\n";
 }

 echo "========================================== \n \n \n";

 
 echo "=================== SETTING A VALUE ===================== \n \n";
 $proxy->setValue('id',$entity,11111111);
 $value = $proxy->getValue('id',$entity);
 echo "new value of id is {$value} and equal to 11111111  \n \n \n";

echo "========================================================= \n \n \n";


echo "=========== SETTING A VALUE (INVALID PROPERTY) =============== \n \n";
 try {
    $value = $proxy->setValue('subject',$entity,'Science');
 }
 catch (Exception $e) {
     echo $e->getMessage() . "\n";
 }

 echo "===================================================== \n \n \n";








 






 