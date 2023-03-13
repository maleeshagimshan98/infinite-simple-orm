<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace Infinite\SimpleOrm\Config;

use Infinite\SimpleOrm\Config\EntityData;


class EntityConfiguration {

    /**
     * json_decoded object of entity configuration
     *
     * @var Object
     */
    private $configuration;

    /**
     * namespace of entity classes
     *
     * @var string
     */
    private $namespace;

    /**
     * array of entities
     *
     * @var array
     */
    private $entities = [];
    

    public function __construct (string $configPath,string $namespace)
    {
        $this->configuration = (object) \yaml_parse(\file_get_contents($configPath),0);
        $this->namespace = $namespace;
    }

    /**
     * create a new entity data object for a entity
     *
     * @param string $name name of the entity
     * @return void
     * @throws \Exception
     */
    private function create (string $name) : void
    {        
        try {
            $this->entities[$name] = new EntityData((object) $this->configuration->$name, $this->namespace,$name);
        }
        catch (\Exception $e) {
            throw new \Exception("Error while creating EntityData object. please check Entity name, configuration, ect.");
        }
    }

    /**
     * check if given entity configuration is already cerated
     *
     * @param string $name name of the entity
     * @return boolean
     */
    private function isAvailable (string $name) : bool
    {
        return isset($this->entities[$name]);
    }

    /**
     * get the entity configuration object for a particular entity
     *
     * @param string $name name of the entity
     * @return EntityData
     */
    public function get (string $name) : EntityData
    {
        if (!$this->isAvailable($name)) {
            $this->create($name);
        }
        return $this->entities[$name];
    }  

}

?>