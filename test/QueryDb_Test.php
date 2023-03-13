<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

require_once('../src/DbConnection/DbConnection.php');
require_once('../src/Query/QueryDb.php');


/**
 * Do the following tests.
 * 
 * execute - select, insert, update, delete
 * transaction
 * commit
 * rollback
 */

 $dbConn = Infinite\SimpleOrm\DbConnection::getConnection([
     'host' => 'localhost', 
     'driver' => 'mysql',
     'username' => 'root',
     'password' => '',
     'db' => 'infinite_orm'
 ]);

 $queryDb = new Infinite\SimpleOrm\Query\QueryDb($dbConn);



 /**
  * execute - select without parameters
  */

  echo "============= SELECT WITHOUT PARAMETERS ================= \n";

  $selectQuery = "SELECT students.* FROM students";
  $queryDb->execute($selectQuery);
  
  var_dump($queryDb->fetchData());
  echo "\n \n \n";

  /**
   * execute - select with parameters
   */

  echo "============= SELECT WITH PARAMETERS ================= \n";

  $selectQuery = "SELECT students.* FROM students WHERE students.id = ?";
  $data = ['123421'];
  $queryDb->execute($selectQuery,$data);
  
  echo "=========== RECORD BELONG TO sara =============== \n";
  var_dump($queryDb->fetchData());
  echo "\n \n \n";

  

  /**
   * execute - insert
   */

   echo "============= INSERTING DATA ================= \n";

   $insertQuery = "INSERT INTO teachers (`teacher_id`, `name`, `subject_id`) VALUES (?,?,?)";
   $data = ['111111','Martin Fowler','3'];
   $queryDb->execute($insertQuery,$data);
   echo "==== NUMBER OF ROWS AFFECTED = 1 =>";
   echo $queryDb->getRowCount() === 1 ? "  TRUE ====== \n" : "FALSE ========= \n";
   echo "\n \n \n";

  /**
   * execute - update
   */

   echo "============= UPDATING DATA ================= \n";

   $updateQuery = "UPDATE students SET name = ? WHERE students.id= ?";
   $data = ['tommy merlin','121232'];
   $queryDb->execute($updateQuery,$data);
   
   echo "==== NUMBER OF ROWS AFFECTED - 1";
   echo $queryDb->getRowCount() === 1 ? "  TRUE ====== \n" : "FALSE ========= \n";
   echo "\n \n \n";

  /**
   * execute - delete
   */

  echo "============= DELETING DATA ================= \n";

  $deleteQuery = "DELETE FROM teachers WHERE teachers.teacher_id = ?";
  $data = ['111111'];
  $queryDb->execute($deleteQuery,$data);
  
  echo "==== NUMBER OF ROWS AFFECTED - 1";
  echo $queryDb->getRowCount() === 1 ? "  TRUE ====== \n" : "FALSE ========= \n";
  echo "\n \n \n";

  /**
   * transaction, commit and rollback
   */

   echo "================= STARTING TRANSACTION ================= \n";

   $queryDb->transaction();

   echo "================= TRANSACTION STARTED ================= \n";

   $insert1 = 'INSERT INTO teachers (`teacher_id`, `name`, `subject_id`) VALUES (?,?,?)';
   $insert2 = 'INSERT INTO students (`id`, `name`, `age`, `country`) VALUES (?,?,?,?)';
   $insert3 = 'INSERT INTO teachers (`teacher_id`, `name`, `subject_id`) VALUES (?,?,?)';

   $data1 = ['00001','test transaction 1','3'];
   $data2 = ['00011','test transaction 2','0','localhost'];
   $data3 = ['00002','test transaction 3',];   

   try {
    echo "======= EXECUTING QUERIES ======== \n";

    $queryDb->execute($insert1,$data1);
    $queryDb->execute($insert2,$data2);
    $queryDb->execute($insert3,$data3);
 
    echo "=======  COMMITING ====== \n";
    $queryDb->commit();

    echo "====== COMMITED ===== \n";
   }
   catch (\Exception $e) {
    echo "==== ROLLBACK ==== \n";

    $queryDb->rollback();
   }


   



