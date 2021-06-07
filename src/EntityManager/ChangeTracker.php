<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * Changes tracker for EntityResult objects
 */

 namespace Infinite\DataMapper;

 use Infinite\DataMapper\Entity\EntityResult;

 /**
  * class for tracking changes in EntityResult object
  */
 class ChangeTracker 
 {
     /**
      * timestamps of creation time of EntityResult objects
      *
      * @var array
      */
     protected  $__createdTimes = [];

     /**
      * first state of EntityResult.
      * clone of actual EntityResult
      *
      * @var EntityResult
      */
     protected $currentFirstState;

     /**
      * detected changes of EntityResult and it's associations
      *
      * @var array
      */
     protected $changes = [];

     /**
      * EntityManager's current entity
      *
      * @var Infinite\DataMapper\Entity entity object
      */
     protected $entity;


     /**
      * constructor
      */
     public function __construct () {
     }

     /**
     * add tracking of entity result object
     *
     * @param string $currentEntityName name of current entity
     * @param EntityResult $result Entity result object
     * @return void
     */
    public function addTracking ($currentEntityName,$result)
    { 
        $this->__createdTimes[$currentEntityName][$result->__getCreatedTime()] = clone $result; //keep track of objects
    }

    /**
     * get the first state of stored EntityResult
     *
     * @return EntityResult
     */
    public function getFirstState ()
    {
        return $this->currentFirstState;
    }

    /**
     * set changed properties and values
     *
     * @param array $changes
     * @return void
     */
    protected function setChanges ($changes)
    {
        if ($changes) {
            $this->changes = array_merge($this->changes,$changes);
        }
    }

    /**
     * set changes association values
     *
     * @param string $key name of associated property
     * @param array $changes array of changed properties and values
     * @return void
     */
    protected function setChangedAssoc ($key,$changes)
    {
        $this->changes["_assoc"][$key] = $changes;        
    }

    /**
     * check if EntityResult object is a tracked one
     * and if tracked, return it's first state, cloned by entity manager
     *
     * @param EntityResult $entity entity result object
     * @return EntityResult|false
     */
    public function isTracked ($entity) 
    {
        $created = $entity->__getCreatedTime();
        $entityName = $entity->name();

        if (!isset($this->__createdTimes[$entityName])) { //IMPORTANT - TRY CHECKING LENGTH OF THIS ARRAY INSTEAD  OF ISSET
            return false;
        }
        if (isset($this->__createdTimes[$entityName][$created])) {
            $this->currentFirstState = $this->__createdTimes[$entityName][$created];
            return $this->__createdTimes[$entityName][$created];            
        }
    }

    /**
     * compare $this->currentFirstState's associations
     * by traversing through properties
     *
     * @param string $key key of $this->firstEntity
     * @param EntityResult $object object to be compared
     * @return array|boolean
     */
    protected function compareAssociations ($key,$object)
    {
        $changes = [];
        $entity = $this->entityContainer->get($object->name()); //associated entity

        /*
        * strictly only iterate over $this->currentFirstState
        */
        foreach ($this->currentFirstState->$key as $prop => $val) {
            //TRY CHECKING IF $object->$key is set, (BUT MAY BE EQUAL TO NULL)
            if ($object->$prop != $val) {
                $mapped = $entity->attribs->getMapped($prop);
                $changes[$mapped] = $object->$prop;
            }
        }
        return count($changes) > 0 ? $changes : false; //IF NO CHANGE DETECTED, RETURN FALSE
    }

    /**
     * compare firstState with current state of entity result
     *
     * @param string $key object key, which need to be checked
     * @param EntityResult $firstState first state of entity result
     * @param EntityResult $current current state of entity result
     * @return mixed
     */
    public function compareChanges ($key,$current)
    {
        $changes = [];
        //comparing object, to check if values changed
        //STRICTLY USE == operator        
        if (\is_object($current->$key) && ($this->currentFirstState->$key == $current->$key)) {
            return false;                      
        }
        //IMPORTANT, STRICT TYPE CHECKING
        if ($current->$key === $this->currentFirstState->$key) { 
            return false; //IF NO CHANGE DETECTED, RETURN FALSE
        }        
        else {
            $mapped = $this->entityContainer->get($this->currentFirstState->name())->attribs->getMapped($key);
            $changes[$mapped] =  $current->$key; //store value changes
            return $changes;
        }
    }

    /**
     * get changed properties in EntityResult
     *
     * @param EntityResult $firstState first state object copy of Entity Result
     * @param EntityResult $current current Entity Result
     * @return array|boolean
     * @throws \Exception
     */
    public function getChanges ($current,$associatedEntities,$entityContainer)
    {
        $this->entityContainer = $entityContainer;
        
        foreach ($this->currentFirstState as $key => $value) {

            if (array_search($key,$associatedEntities) !== false) { // if prop is  an associated entity,                    
                if (!($current->$key instanceof EntityResult)) {
                    throw new \Exception("Associated_Entity_Not_Instance_Of_EntityResult");
                }
                //greedy way, skip traversing if entity not changed
                if ($this->currentFirstState->$key == $current->$key) {
                    continue;
                }
                $this->setChangedAssoc($key,$this->compareAssociations($key,$current->$key));                                    
                continue;                
            }

            $this->setChanges($this->compareChanges($key,$current));
        }
        return count($this->changes) >0 ? $this->changes : false;
    }
}




 ?>
