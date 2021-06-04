<?php

/**
* © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
* pdo connection class
*/

/**
 * database connection class
 */
class pdoConnection  {

    private $username = "root";
    private $password = "";    
    private $dsn = "mysql:host=localhost;dbname=dms_llmp_test";

    public function __construct () {
        try {
        $this->db = new PDO($this->dsn, $this->username, $this->password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        //echo json_encode($this->db);                       
        return $this->db;
        }
        catch (PDOException $e) {
            //echo $e->getMessage();
            throw new Exception("Database_Connection_Failed");
        }
    }

    public function connection () {
        return $this->db;
    }
}
?>