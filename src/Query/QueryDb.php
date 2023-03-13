<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Query;

/**
 * class for querying database with pdo objects
 */
class QueryDb{

    /**
     * PDO connection object
     *
     * @var \PDO
     */
    private $conn;

    /**
     * Sql query string
     *
     * @var String
     */
    private $sql;

    /**
     * Prepared statement
     *
     * @var \PDOStatement 
     */
    private $statement;

    /**
     * prepared/or not state of current Sql query
     *
     * @var Boolean
     */
    private $prepared = false;

    /**
     * construct
     *  
     * @param Object PDO Object
     */
    public function __construct ($conn)
    {
        $this->conn = $conn;
    }

    /**
     * check type of variable to be bounded
     *
     * @param mixed $param
     * @return string
     */
    private function checkType (mixed $param) : string
    {
        $type = gettype($param);
        switch ($type) {
            case "integer":
                $pdoType = \PDO::PARAM_INT;
            break;
            case "string" :
                $pdoType = \PDO::PARAM_STR;
            break;

            case "boolean" :
                $pdoType = PDO::PARAM_BOOL;  
            break;

            case "null" : 
                $pdoType = PDO::PARAM_NULL;
            break;
        }
        return $pdoType;
    }

   /**
    * bind parameter value one by one
    * to prepared statement
    *
    * @param array $params
    * @return void
    */
    private function bindValue(array $params) : void
    {
        if (count($params) > 0) {
            for ($i = 0; $i < count($params); $i++) {
                $pdoType = $this->checkType($params[$i]);
                $this->statement->bindValue($i + 1, $params[$i], $pdoType);
            }
        }
    }

    /**
     * prepare sql statement if previous sql is not equal to the given sql query
     *
     * @param string $query
     * @return void
     */
    private function initQuery (string $query) : void
    {
        if ($this->sql !== $query) {            
            $this->sql = $query;
            $this->statement =  $this->conn->prepare($this->sql);             
            $this->prepared = true;                      
        }        
    }

    /**
     * begin a database transaction
     * 
     * @return void
     * @throws \Exception
     */
    public function transaction () : void
    {
         if (!$this->conn->beginTransaction()) {
             throw new \Exception('Database_Transaction_Start_Failed');
        }
    }

    /**
     * commit the transaction
     * 
     * @return void
     * @throws \Exception
     */
    public function commit () : void
    {
        if(!$this->conn->commit()) {
            throw new \Exception('Database_Transaction_Commit_Failed');            
        }        
    }

    /**
     * rollback transaction
     * 
     * @return void
     * @throws \Exception
     */
    public function rollback () : void
    {
        if(!$this->conn->rollBack()) {
            throw new \Exception('Transaction_Rollback_Failed');
        }        
    }
 
    /**
     * execute the prepared sql statements
     *
     * @param string $query
     * @param array $data
     * @param boolean $bindValue
     * @return void
     * @throws \Exception
     */
    public function execute (string $query, array $data = [], $bindValue = false) : void
    {
        try {
            $this->initQuery($query);
            /*
             * bind values to prepared statements
             * if explicitly stated in $params
             */
            if ($bindValue) {                                                            
                $this->bindValue($data);
            }
            empty($data) ? $this->statement->execute() : $this->statement->execute($data);
        }
        catch (\Exception $e) { 
            /* ============== TEST ==================== */            
            echo json_encode($e->getMessage()); /** Comment in Production */
            throw new \Exception("Database_Query_Execution_Error");
        } 
    }

    /**
     * fetch retrieved data
     *
     * @return array
     */
    public function fetchData () : array
    {        
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * get the row count affected by an insert/ update/ delete query
     * 
     * @return integer
     */
    public function getRowCount () : int
    {
        return $this->statement->rowCount();
    }


    /**
     * ===================================
     * MOVE THIS FUNCTION TO OTHER CLASS
     * ==============================
     */

    public function batchWrite ($action,$sqlStatementStack)
    {
        $this->transaction();
        foreach ($sqlStatementStack as $key) {
            $isDone = $this->execute((object)[
                "sql" => $key[0],
                "data" => $key[1]
            ]);
        }

        if ($isDone) {
            $this->commit();
            return $isDone;
        }
        else {
            $this->rollback();
            throw new \Exception("Database_Query_Failed");
        }
    }
}
?>