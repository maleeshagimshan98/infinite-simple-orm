<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Query;

class QueryBuilder {
    
    private $query;
    private $action;
    private $currentTable;
    private $select = [];
    private $from;
    private $insert = [];
    private $update = [];
    private $delete = [];
    private $leftJoin = [];
    private $orderBy = [];
    private $limit;
    private $offset;
    private $where = [];
    private $groupBy = [];

    public function __construct ()
    {        
    }

    /**
     * prepend column names with current table
     *
     * @param array $columns
     * @return array formatted array of column names
     */
    private function prependTableName (string $tableName, array $columns) : array
    {
        $prepended = [];
        foreach ($columns as $index => $column) {
            $prepended[$index] = "{$tableName}.{$column}";
        }
        return $prepended;
    }
    
    private function initNewQuery (string $action) : void
    {
        $this->action = $action;
        $this->query = "";        
    }

    /**
     * add from table
     *
     * @param string $table
     * @return self
     */
    public function from (string $table) : self
    {
        $this->initNewQuery("SELECT");
        $this->currentTable = $table;
        $this->from = $table;
        return $this;
    }

    /**
     * select columns from a table
     *
     * @param array $columns
     * @return self
     * @throws \Exception
     */
    public function select(array $columns = null) : self
    {
        if ($this->action !== "SELECT") {
            //... Not a supported action - CHECK for sub queries
            throw new \Exception('Cannot select - select is not the current operation');            
        }

        if (!isset($columns)) {
            //... select everything eg:- user.*
            $this->select[$this->currentTable] = ["*"];
        }

        if (\is_array($columns) && empty($columns)) {
            throw new \Exception('You must specify columns for selecting'); //... No columns specified
        }

        if (\is_array($columns)) {
            $this->select[$this->currentTable] = $columns;
        }
        return $this;
    }

    /**
     * make a left join
     *
     * @param string $tableName
     * @param array $condition
     * @return self
     */
    public function leftJoin(string $tableName, array $condition) : self
    {
        $this->currentTable = $tableName;
        $this->leftJoin[$tableName] = $condition; //... need ? marks ?????
        return $this;
    }

    /**
     * add order by clause
     *
     * @param string $column coulumn name
     * @param string $type order type (ASC/DESC)
     * @return self
     */
    public function orderBy (string $column, string $type = "ASC") : self
    {
        $this->orderBy[0] = $column; //... how to get the table name? eg :- ORDER BY user.id ASC
        $this->orderBy[1] = $type;
        return $this;        
    }

    /**
     * add group by clause
     *
     * @param string $column
     * @return self
     */
    public function groupBy (string $column) : self
    {
        $this->groupBy = $column;
        return $this;
    }

    /**
     * set the limit and offset of the result
     *
     * @param string $limit
     * @param string $offset
     * @return self
     */
    public function limit(string $limit, string $offset) : self
    {
        $this->offset = $offset;
        $this->limit = $limit;
        return $this;        
    }

    /**
     * add where condition
     *
     * @param array $condition
     * @return self
     */
    public function where(array $condition) : self
    {
        $this->where = $condition;
        return $this;
    }    

    /**
     * insert data into a table
     *
     * @param string $table
     * @param array $values
     * @return self
     * @throws \Exception
     */
    public function insert(string $table, array $values) : self
    {
        if (\count($values) <= 0) {
            throw new \Exception('No column names given for inserting. Check your entity definition'); //... columns must be > 0
        }
        $this->initNewQuery("INSERT");
        $this->insert["table"] = $table;
        $this->insert["values"] = $values;
        return $this;
    }    

    /**
     * update data in a table
     *
     * @param string $table
     * @return self
     */
    public function update(string $table,array $values,array $condition = []) : self
    {
        $this->initNewQuery("UPDATE");
        $this->update["table"] = $table;
        $this->update["values"] = $values;
        $this->update["condition"] = $condition;
        return $this;
    }    

    public function delete(string $table, array $condition = []) : self
    {
        $this->initNewQuery("DELETE");
        $this->delete['table'] = $table;
        $this->delete['condition'] = $condition;
        return $this;
    }

    /**
     * build condition string according to given data
     * if multi dimension array was given as the parameter,
     * builds the conditional string and join them with 'AND' iterating all elements in array
     *
     * @param array $condition
     * @return string
     */
    private function buildConditionString (array $condition) : string
    {
        $conditionString = "";

        /**
         * need some refactoring for this function, to validate arguments
         * eg :- $conditions = [ ['john', '=', 'doe'], ['age', '=', '19'], 'foo' => 'bar'], this one fails
         */

        if(array_key_exists(0,$condition) && \is_array($condition[0])) {
            for ($i=0; $i< \count($condition); $i++) {
                $conditionString .= $this->buildConditionString($condition[$i]);
                if ($i <= \count ($condition)-2) {
                    $conditionString .= " AND ";
                }
            }
            return $conditionString;
        }       
        
        elseif (count($condition) === 3) {
            $conditionString = "{$condition[0]} {$condition[1]} {$condition[2]}";
            //$conditionString .= $condition[2] == '?' ? " ?" : "{$condition[2]}";
            return $conditionString;
        }
        else {
            //... simply 'equals' conditions with associative arrays
            //... eg - ['id' => '123'] into "id = 123"
            $str = '';
            foreach ($condition as $key => $val) {
                $str .= "{$key} = {$val}";
                if (array_key_last($condition) !== $key) {
                    $str .= " AND ";
                }
            }
            return $str;
        }
    }

    /**
     * parse condition and return condition string
     *
     * @param array $condition
     * @return string
     */
    private function parseCondition (array $condition) : string
    {
        $conditionString = "";
        if (\is_callable($condition)) {
            $conditionString = \call_user_func($condition);
        }        
        else {
            $conditionString .= $this->buildConditionString($condition);
        }   
        return $conditionString;     
    }
    
    /**
     * create left join string for all left joins
     *
     * @return string
     */
    private function __leftJoin () : string
    {
        $joinString = "";
        foreach ($this->leftJoin as $table => $condition) {
            $joinString .= "LEFT JOIN {$table} ON {$this->parseCondition($condition)} ";
        }
        return $joinString;
    }

    /**
     * create where string with given conditions
     *
     * @return string
     */
    private function __where () : string
    {
        return "WHERE {$this->parseCondition($this->where)} ";
    }

    /**
     * build the select query
     *
     * @return void
     */
    private function __select () : void
    {
        $this->query = "{$this->action} ";
        //... adding columns of 'FROM' table, and all left joins after perpending table names
        foreach ($this->select as $table => $select) {
            $this->query .= \implode(", ",$this->prependTableName($table,$select));
            if (array_key_last($this->select) !== $table) {
                $this->query .= ", ";
            }
        }
        //... add FROM table name
        $this->query .= " FROM {$this->from} ";
        //... check if there is a left join
        if (\count($this->leftJoin) > 0) {
            $this->query .= $this->__leftJoin();
        }
        //... add where clause
        if (\count($this->where) > 0) {
            $this->query .= $this->__where();          
        }
        //... add order by
        if (\count($this->orderBy) > 0) {
            $this->query .= "ORDER BY {$this->orderBy[0]} {$this->orderBy[1]} ";
        }
        //... add limit
        if ($this->limit) {
            $this->query .= "LIMIT {$this->limit}";            
        }
        //... add offset
        if ($this->offset) {
            $this->query .= ",{$this->offset} ";
        }               
    }

    /**
     * build the insert query
     *
     * @return void
     */
    private function __insert () : void
    {
        $columns = \implode(',',array_keys($this->insert["values"]));
        $values = \implode(',',array_values($this->insert["values"]));
        $this->query .= "{$this->action} INTO {$this->insert["table"]} ({$columns}) VALUES ({$values})";  
    }

    /**
     * build the update query
     *
     * @return void
     */
    private function __update () : void
    { 
        $this->query = "{$this->action} {$this->update["table"]} SET ";        
        foreach ($this->update["values"] as $column => $value) {
            $this->query .= "{$column} = {$value}";
            if ($column !== \array_key_last($this->update['values'])) {
                $this->query .= ", ";
            }
        }
        if (\count($this->update["condition"]) > 0) {
            $this->query .= " WHERE {$this->parseCondition($this->update['condition'])} ";
        }        
    }

    /**
     * build the delete query
     *
     * @return void
     */
    private function __delete () : void
    {
        $this->query = "{$this->action} FROM {$this->delete['table']}";
        $this->query .= " WHERE {$this->parseCondition($this->delete['condition'])} ";       
    }

    /**
     * get the created query string
     *
     * @return string
     * @throws \Exception
     */
    public function sql () : string
    {
        switch ($this->action) {
            case "SELECT" :
                $this->__select(); 
            break;

            case "INSERT" : 
                $this->__insert();
            break;

            case "UPDATE" : 
                $this->__update();
            break;

            case "DELETE" : 
                $this->__delete();
            break;

            default :
              throw new \Exception('Cannot build the query, you have no action specified'); //... invalid action
            break;
        }
        return $this->query;
    }

    /**
     * reset the query
     * 
     * @return void
     */
    public function reset () : void
    {
        $this->query;
        $this->action;
        $this->currentTable;
        $this->select = [];
        $this->from;
        $this->insert = [];
        $this->update = [];
        $this->delete = [];
        $this->leftJoin = [];
        $this->orderBy = [];
        $this->limit;
        $this->offset;
        $this->where = [];
    }

}