<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Config;

class Attribute {

    /**
     * name of the attribute
     */
    private $name;

    /**
     * column name of the attribute
     */
    private $columnName;

    /**
     * type of the attribute
     */
    private $type;

    /**
     * defined length of the attribute
     */
    private $length;

    /**
     * if the attribute is a auto incremented value or not
     */
    private $autoIncrement;

    public function __construct(string $name, $config)
    {
        $this->name = $name;
        //... detailed definition
        if (is_array($config)) {            
            $this->columnName = $config['name'] ?? $name;
            $this->type = $config['type'] ?? 'string';
            $this->length = $config['length'] ?? null;
            $this->autoIncrement = $config['autoIncrement'] ?? false;            
        }
        //... simple definition
        else {
            $this->columnName = $config;
            $this->type = 'string';
            $this->length = null;
            $this->autoIncrement = false;               
        }                
    }

    /**
     * check if attribute is writable
     * 
     * @return boolean
     */
    public function isWritable () : bool
    {
        return !$this->autoIncrement;
    }
    
    /**
     * Get the value of name
     * 
     * @return string
     */ 
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * get the defined length of attribute
     * 
     * @return integer|null
     */
    public function getLength () : mixed
    {
        return $this->length;
    }

    /**
     * Get the value of columnName
     */ 
    public function getColumnName() : string
    {
        return $this->columnName;
    }

    /**
     * Get the value of type
     */ 
    public function getType() : string
    {
        return $this->type;
    }
}



 ?>