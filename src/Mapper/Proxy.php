<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Mapper;

class Proxy {

    /**
     * reflection class instance for a particular entity class
     *
     * @var \ReflectionClass
     */
    private $refelctionClass;

    public function __construct ()
    {        
    }
    
    /**
     * set the reflection class to be used by the class
     */
    public function setReflectionClass (\ReflectionClass $reflectionClass) : void
    {
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * set values for a particular entity class instance through reflection class
     *
     * @param string $name property name of the object
     * @param mixed $obj object
     * @param mixed $value the value
     * @return void
     * @throws \Exception
     */
    public function setValue (string $name, $obj, $value = null) : void
    {
        if (!$this->reflectionClass->hasProperty($name)) {
            throw new \Exception("Attribute name {$name} is undefined in entity class ". \get_class($this));
        }
        
        $property = $this->reflectionClass->getProperty($name);
        $property->setAccessible(true);
        
        if ($property->isPrivate()) {
            $property->setValue($obj,$value);
        }       
    }
    
   /**
    * get values of particular entity class instance through reflection class
    *
    * @param string $name anme of the property
    * @param mixed $obj object
    * @return mixed
    * @throws \Exception
    */
    public function getValue (string $name,$obj)
    {
        if (!$this->reflectionClass->hasProperty($name)) {
            throw new \Exception("Attribute name {$name} is undefined in entity class ". \get_class($this));
        }
        $property = $this->reflectionClass->getProperty($name);
        $property->setAccessible(true);        
        return $property->getValue($obj);        
    } 
}