<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 * query builder wrapper - wraps Envms\FluentPDO\Query essential methods
 */

namespace Infinite\DataMapper\QueryBuilder;

use \Infinite\DataMapper\QueryDb;

/**
 * query builder wrapper - wraps Envms\FluentPDO\Query essential methods
 */
class QueryBuilderWrapper
{

    /**
     * sql query statement
     *
     * @var string
     */
    public $sql;

    /**
     * current CRUD action
     *
     * @var string
     */
    public $action = "select";

    /**
     * query builder objcet
     *
     * @var \Envms\FluentPDO\Query
     */
    protected $qbo;

    /**
     * database querying class
     *
     * @var \Infinite\DataMapper\QueryDb
     */
    public $queryDb;


    /**
     * constructor
     *
     * @param \PDO object $db database connection
     */
    public function __construct ($db)
    {
        $this->qbo = new \Envms\FluentPDO\Query($db);
        $this->queryDb = new QueryDb($db);
    }

    /**
     * check if array is associative
     * 
     * @param array $arr target array to be checked
     * @return boolean
     */
    protected function is_assoc ($arr)
    {
        if (is_array($arr) && count($arr) == 0)
        {
            return false;
        }
        if (is_array($arr) && array_keys($arr) !== range(0,count($arr) -1))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * check if an array is multi dimantional array
     *
     * @param array $array
     * @return boolean
     */
    protected function is_multi_dim (array $array)
    {   
        $isArray = false;     
        foreach ($array as $key) 
        {
            $isArray = is_array($key);
        }
        return $isArray;
    }

     /**
     * fetch table name and column name 
     * given in following format - table_name.column_name
     *
     * @param string $table_name
     * @return array ["table_name","column_name]
     */
    public function fetchTableName ($table_name) { //echo json_encode($table_name);
        $nameComponents = explode(".",$table_name);
        return count($nameComponents) == 1 ? $nameComponents[0] : $nameComponents;
    }
    
    /**
     * from table
     * 
     * @param string $table - table name
     * @return self
     */
    public function from ($table)
    {
            $this->sql = $this->qbo->from($table)->select(null); //echo \json_encode((string)$this->sql);
            return $this;
    }

    /**
     * create an alias for a column or table
     *
     * @param array $data - paramters - [table_name.column_name, alias]
     * @return string|boolean
     * @throws \Exception
     */
    public function alias ($data)
    {
        if (!is_array($data) || !\is_string($data[1]) || \is_numeric($data[1]))
        {
            return false;
        }        //IMPORTANT - CHECK BELOW
        if(!$table_name)
        { //IMPORTANT - CHECK
            throw new \Exception("Column_Name_Undefined");
        }
        return $table_name ." AS ".$data[1];
    }

    /**
     * select from table
     * 
     * @param string|array|null $columns - columns to be selected
     * @return self
     * @throws \Exception
     */
    public function select ($columns=null)
    {
        $this->action = "select";

        //select all
        if(empty($columns))
        {            
            $this->sql = $this->sql->select(); 
            return $this;
        }
        
        if (is_array($columns))
        { //echo json_encode($columns);
            $arr = [];
            foreach ($columns as $key)
            {  //echo json_encode($key."/n");
                if (is_array($key))
                {
                    $arr[] = $this->alias($key);
                }
                else
                {
                    $arr[] = $key;
                }
            }
            $this->sql =  $this->sql->select($arr);
        }            
        
        return $this;
    }

    /**
     * set where clause
     * 
     * @param array $condition - [..., table_name.column_name => value]
     * @return self
     * @throws \Exception
     */
    public function where ($condition)
    {
        if (!\is_array($condition))
        {
            throw new \Exception("Invalid_Parameter_Type");
        }

        $this->sql = $this->sql->where($condition);
        return $this;
    }

    /**
     * process where paramters to match with Envms\FluentPDO\Query::where()
     *
     * @param string $key
     * @param string $val
     * @return self
     */
    private function processWhereParams ($key,$val)
    {
        return array($key => $val);
    }

    /**
     * process insert values
     *
     * @param array $columns table columns
     * @param array $values column values
     * @return array associative array ["column" => "value"]
     */
    protected function processInsertValues ($columns,$values)
    {  
        $arr = [];
        for ($i = 0; $i< count($columns); $i++)
        {
            $arr[$columns[$i]] = $values[$i];
        }
        return $arr;
    }

    /**
     * insert data to a table
     * 
     * @param string $table - table name
     * @return self
     */
    public function insert ($table,$columns,$values)
    {
        $this->action = "insert";
        $values = $this->processInsertValues($columns,$values);
        $this->sql = $this->qbo->insertInto($table)->values($values);
        return $this;
    }

    /**
     * update data in a table row
     *
     * @param string $table
     * @param array $values associative array containing values
     * @param string|array $primary primary key(s)
     * @return 
     */
    public function update ($table,$values,$primary)
    {
        $this->action = "update";
        $this->sql = $this->qbo->update($table)->set($values);
        if (\is_array($primary)) {
            foreach($primary as $key => $val) {
                $this->sql->where($key,$val); //dont use $values
            }           
        }
        return $this;
    }

    /**
     * delete data in a table row
     *
     * @param string $table
     * @return 
     */
    public function delete ($table)
    {
        $this->action = "delete";
        $this->sql = $this->qbo->update($table); //IMPORTANT - CHECK
        return $this->sql;
    }

    /**
     * left join wrapper
     * 
     * @example $tables - e.g. -
     *        [table.column,joining_table.column,'='],
     *        [ [table.column,joining_table.column],..., [...] ]
     *        [ [table.column,[values]],..., [...] ]
     * 
     * @param array $tables - tables to join
     * @return self
     * @throws \Exception
     */
    public function leftJoin (array $tables)
    { //echo json_encode($tables);
        
        if ($this->is_multi_dim($tables))
        {
            foreach($tables as $key) 
            {
                $this->leftJoin($key);
                continue;                
            }
        }
       
        $primary = $this->fetchTableName($tables[0]);
        if (!is_array($primary))
        {
            throw new \Exception("Invalid_Table_Name");
        }
        $joinString = $primary[0]." ON ";
                            
        $joinString .= $this->createJoinStatement($tables[0],$tables[1],$tables[2]??"=");
                                                    
        
        $this->sql = $this->sql->leftJoin($joinString);
        return $this;
    }

    /**
     * create the join string
     *
     * @param string $primary primary table
     * @param string|array $joining_table
     * @param string $comparision
     * @return string
     */
    protected function createJoinStatement ($primary,$joining_table,$comparision = "=")
    {
        if (is_array($joining_table)) {
            $joinStr = $primary.$comparision;
            
            $orCount = 0; //IMPORTANT - NOT TESTED
            $or = "OR";
            foreach ($joining_table as $table) {
                if ($orCount >= count($joining_table)-1) {
                    $or = "";
                }
                $joinStr .= $table.$or;
                $orCount++;
            }
           
            return $joinStr; 
        }        
        $joinStr = $primary.$comparision.$joining_table;
        return $joinStr;
    }
    
    /**
     * order by clause wrapper
     * 
     * @param string $prop - table_name.column_name,
     * @param string $type - ASC/DESC
     * @return self
     */
    public function orderBy ($prop,$type = "ASC")
    {
        $this->sql = $this->sql->orderBy($prop." ".$type); // IMPORTANT - CHECK
        return $this;
    }

    /**
     * limit clause wrapper
     *
     * @param array $limit
     * @return self
     */
    public function limit ($limit)
    {
        $start = (string) $limit[0];
        if (count($limit) == 1) {            
            $this->sql = $this->sql->limit($start);
            return $this;
        }
        $end = (string) $limit[1];
        $this->sql = $this->sql->limit($start.",$end");
        return $this;
    }

    /**
     * execute the query
     * 
     * @param object $data data to bound to prepared statement
     * @return mixed
     */
    public function execute ($data = null)
    {
        switch ($this->action) {
            case "select" : 
                $result = $this->queryDb->select($data);
            break;
        }
        //echo \json_encode($result);
        return $result;
    }

    /**
     * create a raw sql statement
     *
     * @param string $sql sql statement
     * @return self
     */
    public function rawSqlStatement ($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * get the sql string we've created
     *
     * @return string
     */
    public function getSqlString ()
    {
        return (string) $this->sql;
    }
}
?>