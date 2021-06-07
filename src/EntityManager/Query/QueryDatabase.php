<?php
/**
 * Author - 2020 -  maleeshagimshan98
 * Permission is hereby granted,free of charge, to any person obtaining a copy of this software (queryDatabase.php) and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
*/
namespace Infinite\DataMapper;

/**
 * class for querying database with pdo objects
 */
class QueryDb {

    /**
     * construct 
     * @param Object PDO Object
     */
    public function __construct ($conn) {
        $this->conn = $conn;
        $this->sql = "";
        $this->statement = false;        
        $this->prepared = false;
        return $this;
    }

    /**
     * prepare sql statement
     * 
     * @return \PDOStatement  PDO prepared statement
     */
    public function prepareStatement () {  
        if (!$this->prepared) {
            $this->statement =  $this->conn->prepare($this->sql);                        
            $this->prepared = true;            
        }        
        return $this->statement;
    }

    /**
     * check types of variables to be bounded
     * 
     * @param Any
     * @return string
     * @throws \Exceptions
     */
    public function checkTypes ($params) {

        $type = gettype($params);

        /**
         * extend further in the future
         */
        switch ($type) {
            case "integer":
                $pdoType = PDO::PARAM_INT;
                return $pdoType;
            break;
            case "string" :
                $pdoType = PDO::PARAM_STR;
                return $pdoType;
        }

    }

    /**
     * begin transaction
     * 
     * @throws \PDOExeption
     */
    public function transaction () {
        $this->conn->beginTransaction();
    }

    /**
     * commit
     * 
     * @return boolean
     * @throws \PDOExeption
     */
    public function commit () {
        $this->conn->commit();        
    }

    /**
     * get last inserted id
     *
     * @return string|integer
     */
    public function lastId () {
        return $this->conn->lastInsertId();
    }

    /**
     * rollback transaction
     * 
     * @return boolean
     * @throws \PDOExeption
     */
    public function rollback () {
        $this->conn->rollBack();        
    }

    /**
     * binds parameter value one by one
     * to prepared statement
     * 
     * @param array values with respecting order of '?' marks
     */
    public function bindValue($params)  {
        if (count($params) > 0) {
            for ($i = 0; $i < count($params); $i++) {
                $pdoType = $this->checkTypes($params[$i]);
                $this->statement->bindValue($i + 1, $params[$i], $pdoType);
            }
        }
    }

    /**
     * prepare all statements, bind values if required
     * before executing statement
     *
     * @param object $params parameters - sql query string, data
     * @return void
     */
    public function setupQuery ($params) {
        if($this->sql !== $params->sql) {            
            $this->sql = $params->sql;            
            $this->prepared = false;                      
        }
        $this->prepareStatement();
        /*
         * bind values to prepared statements
         * if explicitly stated in $params
         */
        if (isset($params->bindValue) && $params->bindValue) {                                                            
            $this->bindValue($params->data);
        }
    }

    /**
     * execute select statements
     * 
     * @param object,array $params
     * @return mixed
     * @throws \Exception
     */
    public function select ($params) {
        try {
            $this->setupQuery($params);
            if (empty($params->data)) {
                $this->statement->execute();
            }
            else {
                $this->statement->execute((array)$params->data); 
            }       
            return $this->fetchData();
        }
        catch (Exception $e) { 
            /*TEST*/ echo json_encode($e->getMessage());
            throw new Exception("Database_Error");
        } 
    }


    /**
     * execute insert statements
     * and returns affected rows
     * 
     * @param object,array $params
     * @return integer|boolean
     * @throws \Exception
     */
    public function insert ($params) {
        try {
            $this->setupQuery($params);        
            $this->statement->execute((array)$params->data);        
            return $this->statement->rowCount();
        }
        catch (Exception $e) {
            /*TEST*/ echo json_encode($e->getMessage());
            throw new Exception("Database_Error");
        } 
    }

    /**
     * updates data for a record and return affected rows ?
     * 
     * @param object $params
     * @return boolean
     * @throws \Exception
     */
    public function update ($params) {
        try {
            $this->setupQuery($params);
            return $this->statement->execute((array)$params->data);
        }
        catch (Exception $e) {
            /*TEST*/ echo json_encode($e->getMessage());
            throw new Exception("Database_Error");
        } 
    }

    /**
     * delete data
     * 
     * @param object $params
     * @return void
     */
    public function delete ($params) {

    }

    /**
     * fetch retrieved data
     *
     * @return object|array
     */
    public function fetchData () {        
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function batchWrite ($action,$sqlStatementStack)
    {
        $this->transaction();
        if ($action == "insert") {
            $isDone = true;
            foreach ($sqlStatementStack as $key) {
                $isDone = $this->insert((object)[
                    "sql" => $key[0],
                    "data" => $key[1]
                ]);
            }
        }
        if ($action == "update") {
            $isDone = true;
            foreach ($sqlStatementStack as $key) {
                $this->update((object)[
                    "sql" => $key[0],
                    "data" => $key[1]
                ]);
            }
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


    /**
     * execute sql statement
     * and returns data
     * 
     * @param object,array
     * @return mixed
     * @throws \Exceptions
     */
    public function executeStatement($params)  {
        if($this->sql !== $params->sql) {            
            $this->sql = $params->sql;            
            $this->prepared = false;                      
        }
        try {
            $this->prepareStatement();       

            switch ($params->action) {
                case "select" :
                    return $this->selectData($params);
                break;

                case "insert" :
                    return $this->insertData($params);
                break;

                case "update" :
                    return $this->updateData($params);
                break;
            }
        }
        catch (Exception $e) { echo json_encode($e->getMessage());
            throw new Exception("Database_Error");
        }     
    }

}
?>