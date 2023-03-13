<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Mapper;

/**
 * A Simple Wrapper Class For Containing Mapped Results And Array Representation Clone Of Entities
 */
class MapperResult {

    /**
     * mapped entities 
     *
     * @var array
     */
    private $result;

    /**
     * clone of mapped entities represented in an array
     * 
     * @var array
     */
    private $clone;

    public function __construct ($result, $clone)
    {  
        $this->result = $result;
        $this->clone = $clone;      
    }

    /**
     * get the entities mapped by the mapper
     *
     * @return array
     */
    public function result () : array
    {
        return $this->result;
    }
    
   /**
    * get the simple array representation of mapped entities
    *
    * @return array
    */
    public function clone () : array
    {
        return $this->clone;        
    } 
}