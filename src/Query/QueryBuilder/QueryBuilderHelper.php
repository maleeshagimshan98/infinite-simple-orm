<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Query;

/**
 * Helper class containing methods to build comparison operations
 */
class QueryBuilderHelper {

    /**
     * build the required comparison string
     *
     * @param array $params
     * @param string $operator
     * @return string
     */
    private static function build (array $params,string $operator) : string
    {
        $string = "";
        for($i=0; $i<count($params); $i++) {
            $string .=  "{$params[$i]} ";
            if ($i <= count($params)-2) {
                $string .= "{$operator} ";
            }                     
        }
        return $string;
    }

    /**
     * Compare using OR
     *
     * @param array $params
     * @return string
     */
    public static function Or(array $params) : string
    {
        return self::build($params,"OR");        
    }

    /**
     * Compare using AND
     *
     * @param array $params
     * @return string
     */
    public static function And(array $params) : string
    {
        return self::build($params,"AND");             
    }

    /**
     * Compare using NOT
     *
     * @param array $params
     * @return string
     */
    public static function Not(array $params) : string
    {
        return self::build($params,"NOT");              
    }

    /**
     * concatenate parameter value
     * if paramter is '?' add it as it is. Otherwise add ` (backtick) to the left and right of the parameter value
     *
     * @param string $param
     * @return string
     */
    private function concatParamValue (string $param) : string
    {
        return $param === "?" ? "?" : "`{$param}`";        
    }

    /**
     * build equals string
     *
     * @param string $param1
     * @param string $param2
     * @return string
     */
    public static function Equal(string $param1, string $param2) : string
    {
        return "{$param1} = {self::concatParamValue($param2)} ";
    }

    /**
     * build not equal string
     *
     * @param string $param1
     * @param string $param2
     * @return string
     */
    public static function NotEqual(string $param1, string $param2) : string
    {
        return "{$param1} != {self::concatParamValue($param2)}";
    }

    /**
     * build greater than string
     *
     * @param string $param1
     * @param string $param2
     * @return void
     */
    public static function Greater(string $param1, string $param2)
    {
        return "{$param1} > {self::concatParamValue($param2)}";
    }

    /**
     * build greater or equal than string (>=)
     *
     * @param string $param1
     * @param string $param2
     * @return void
     */
    public static function GreaterOrEqual(string $param1, string $param2)
    {
        return "{$param1} >= {self::concatParamValue($param2)}";
    }

    /**
     * build less than string
     *
     * @param string $param1
     * @param string $param2
     * @return void
     */
    public static function Less(string $param1, string $param2)
    {
        return "{$param1} < {self::concatParamValue($param2)}";
    }

    /**
     * build equal or less than string (<=)
     *
     * @param string $param1
     * @param string $param2
     * @return void
     */
    public static function LessOrEqual(string $param1, string $param2)
    {
        return "{$param1} <= {self::concatParamValue($param2)}";
    }
}