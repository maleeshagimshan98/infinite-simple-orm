<?php
/**
 * © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
 *
 * Entity Manager class
 */

 namespace Infinite\DataMapper;

 include_once dirname(__DIR__)."/Products.php"; 
 include_once dirname(__DIR__)."/sku.php";
 include_once dirname(__DIR__)."/product_sku.php";
 include_once dirname(__DIR__)."/orders.php";

 use Infinite\DataMapper\EntityContainer;
 use Infinite\DataMapper\Entity\EntityResult;
 use Infinite\DataMapper\ChangeTracker;
 use Infinite\DataMapper\QueryBuilder\QueryBuilderWrapper;
 use Infinite\DataMapper\QueryDb;
 use Infinite\DataMapper\Pagination;
 
 /**
  * Entity manager class
  */
 class EntityManager {

    /**
     * PDOConnection
     *
     * @var \PDO object
     */
    private $connection;

    /**
     * Entities
     *
     * @var Infinite\DataMapper\EntityContainer 
     *
     */
    public $entities;

    /**
     * current entity
     *
     * @var Infinite\DataMapper\Entity
     */
    protected $currentEntity;

    /**
     * current associated entities
     *
     * @var Infinite\DataMapper\EntityContainer
     */
    protected $currentAssociated;

    /**
     * sql statement created when queriying entities
     *
     * @var \PDOStatement
     */
    protected $sqlStatement;

    /**
     * sql statements created when inserting data
     * have multiple sql statements
     * execute all in one transaction 
     *
     * @var array
     */
    protected $sqlStatementStack = [];

    /**
     * CRUD action - select,insert,update,delete
     *
     * @var string
     */
    protected $action = "";

    /**
     * constructor
     *
     * @param object $config
     * @return void
     */
    public function __construct (object $config)
    {        
        $this->initConfig($config);        
    }

    /**
     * initialize Entity manager with configuration
     *
     * @param object $config  configuration object
     * @return void
     * @throws \Exception
     */
    protected function initConfig (object $config)
    {
        if (empty($config->connection)) {
            throw new \Exception("Database_Connection_Not_Found");
        }
        
        $this->connection = $config->connection; //CONSIDER REMOVING THIS PROPERTY
        $this->entities = new EntityContainer(json_decode(file_get_contents(dirname(__DIR__)."/config/entity_definition.json")));
        $this->currentAssociated = new EntityContainer();
        $this->queryBuilder = new QueryBuilderWrapper($this->connection);
        $this->queryDb = new QueryDb($this->connection);
    }

    /**
     * return a new entity result object of a respective entity class - when inserting new data
     *
     * @param string $entity entity name
     * @return Infinite\DataMapper\Entity\EntityResult Entity result object
     * @throws \Exception
     */
    public function entity (string $entityName)
    {   
        return $this->entities->entity($entityName)->getEntityResult();        
    }

    /**
     * set current entity
     *
     * @param string $entity entity name
     * @return void
     */
    protected function setCurrentEntity ($entity)
    {
        $this->currentEntity = $this->entities->get($entity);
    }

    /**
     * clear current entity
     *
     * @return void
     */
    protected function clearCurrentEntity ()
    {
        $this->currentEntity = null;
    }

    /**
     * prepare basic parts of query string
     * for getting an entity from database
     *
     * @param \Infinite\DataMapper\Entity\ $entity entity object
     * @return void
     */
    protected function prepareGet ($entity)
    { /* TESING */ //echo \json_encode($entity);
        $this->sqlStatement = $this->queryBuilder->from($entity->table())
        ->select($entity->mapAttribsToColumn());
        return $this->sqlStatement;
    }

    /**
     * get entity from database
     *
     * @param string $entity entity name
     * @param array|null $id get a row based on id [column_name,value]
     * @return self
     * @throws \Exception
     */
    public function get ($entity,$id = null)
    {  
        $this->action = "select";
        $this->setCurrentEntity($entity); //echo json_encode($this->currentEntity);
        $sql = $this->prepareGet($this->currentEntity);
        if (!empty($id)) {
            $sql = $sql->where([
                $this->currentEntity->mapAttribsToColumn($id[0]) => $id[1]
            ]);        
        }
        return $this;
        /** TESTING */ //echo json_encode();
    }

    /**
     * associate entities in the result
     *
     * @param string $entity associated entity name
     * @param mixed  $joiningEntityId entity id (or foreign key) of associated entity
     * @return self
     * @throws \Exception
     */
    public function associate ($entity,$joiningEntityId = null)
    {
        $associated = $this->currentEntity->associations->get($entity);
        $target = $this->entities->get($associated->target);        
        $this->currentAssociated->set($associated->target,$target); //IMPORTANT - CHECK IF THIS IS NEEDED - $target
        
        $this->sqlStatement = $this->sqlStatement->leftJoin([
            $target->mapAttribsToColumn($associated->refer),
            $this->currentEntity->mapAttribsToColumn($associated->inverse)
            ])->select($target->mapAttribsToColumn());
        
            if (is_array($joiningEntityId))
            {
                $this->sqlStatement = $this->sqlStatement->where(
                    [
                        $target->mapAttribsToColumn($joiningEntityId[0]) => $joiningEntityId[1]
                    ]
                );
            }
            return $this;
    }
    
    /**
     * hydrate entity with data fetched from database
     *
     * @param array|object $res data fetched from database
     * @return array
     */
    protected function hydrate ($res) 
    {
        $this->tracker = new ChangeTracker();   
        if (!$res) {
            return $res;
        }

        $currentEntity = $this->currentEntity;
        $entityCollection = [];
        foreach ($res as $key) 
        {
            $result = $currentEntity->hydrate($key);
             //keep track of objects
            if (!$this->currentAssociated->is_empty())
            {
               $result = $this->hydrateAssociated($result,$key);
            }
            $this->tracker->addTracking($currentEntity->name(),$result);
            $entityCollection[] = $result;
        } //echo json_encode($entityCollection);
        return $entityCollection;
    }

    /**
     * hydrate associated entity of the current entity
     * (only single object from result set,
     * run this multiple times to hydrate all entities, and associated ones)
     *
     * @param Infinite\DataMapper\Entity\EntityResult $entity entiy result object
     * @param array|object $data data fetched from database
     * @return Infinite\DataMapper\Entity\EntityResult
     */
    protected function hydrateAssociated ($entity,$data) 
    {
        $associated = $this->currentAssociated->getAll(); 
        foreach ($associated as $key => $value) 
        {
            $assocEntityResult = $this->entities->entity($key)->hydrate($data);
            $entity->set($key,$assocEntityResult);
            //$this->tracker->addTracking($assoc->name(),$assoc);
        }
        return $entity;
    }

    /**
     * insert data to database - create an insert query
     *
     * @param EntityResult $entity entity result object
     * @param mixed $value values to be inserted
     * @return void
     */
    protected function insertData ($entity,$value = null)
    {
        $this->action = "insert";
        $this->setCurrentEntity($entity->name());
        if (isset($value)) {
            $entity->__setFromObj((object)$value); // $value must be an object
        }

        $writableAttribs = $this->currentEntity->attribs->getWritableAttribsMapped();
        $insertDataArr = $this->currentEntity->insertProps($entity);

        $sqlStatement[] = $this->queryBuilder->insert(
            $this->currentEntity->table(),
            $writableAttribs,
            $insertDataArr
        )->getSqlString(); //echo $sqlStatement[0];

        $sqlStatement[] = $insertDataArr;
        $this->sqlStatementStack[] = $sqlStatement;
        //ONLY MAIN ENTITY IS BEING INSERTED

    }

    /**
     * process primary keys to ypdate entities
     *
     * @param EntityResult $entityResult entity result object
     * @return array
     */
    protected function processPrimaryKeys ($entityResult)
    {
        $this->setCurrentEntity($entityResult->name());
        $primaryKey = $this->currentEntity->primary();
        $primaryKeyValues = [];
        $values = [];
        
        foreach ($primaryKey as $key) {
            $attrInverseMap = $this->currentEntity->attribs->getInverseMap();
            $primaryKeyMapped = $attrInverseMap[$key]; //check
            $primaryKeyValues[$key] = $entityResult->$primaryKeyMapped;
            $values[] = $entityResult->$primaryKeyMapped;
        }
        return ["pk" => $primaryKeyValues, "values" => $values];
    }

    /**
     * update entity values in database
     *
     * @param EntityResult $entity entity result object
     * @param array $changes - array with key => values of changes (keys have been mapped to column names)
     * @return void
     */
    protected function updateData ($changes,$primaryKeys)
    {
        $this->action = "update";       
        
        $sqlStatement[] = $this->queryBuilder->update(
            $this->currentEntity->table(),
            $changes,
            $primaryKeys["pk"]
        )->getSqlString();

        $sqlStatement[] = array_merge(array_values($changes),$primaryKeys["values"]);
        $this->sqlStatementStack[] = $sqlStatement;
    }

    /**
     * process changed properties of EntityResult to update
     *
     * @param EntityResult $entity entity result object
     * @param array $changes associated array with changes
     * @return void
     */
    protected function processChanges ($entity,$changes)
    {
        if (isset($changes["_assoc"])) {
            $assocChanges = $changes["_assoc"];
            $primaryChanges = array_diff($changes,$assocChanges);

            foreach ($assocChanges as $key => $value) {
                //IMPORTANT - ADD NECESSARY UPDATES FOR ASSOCIATED ENETITIES
                $this->updateData();//check
            }
        }
        else {
            $primaryChanges = $changes;
        }                
        $this->updateData($primaryChanges,$this->processPrimaryKeys($entity));
    }

    /**
     * save an entity to database
     * insert new row if new data, or update if retrieved entity has been changed
     *
     * @param EntityResult $entity entity object
     * @param array $values associated array of values - ["column_1" => "value", "column_2" => "value"]
     * @return void
     */
    public function save ($entity,$values = null)
    {        
        $firstState = $this->tracker->isTracked($entity); //check
        if ($firstState) {
            $this->setCurrentEntity($firstState->name());
            $assoc = $this->currentAssociated->getAll();
            $changes =  $this->tracker->getChanges($entity,$assoc,$this->entities);
            if (!$changes) {
                return; // if no changes, no need to save
            }            
            $this->processChanges($entity,$changes);
        }
        else  {
            $this->insertData($entity,$values);
            return $this;
        }        
        return $this;                
    }

    /**
     * delete an entity from database
     *
     * @return void
     */
    public function delete ()
    {

    }

    /**
     * execute the current CRUD Action statement
     *
     * @return void
     */
    public function go ($data = null)
    {
        if ($this->action === 'select')
        {
            $res = $this->queryDb->select((object) [
                "sql" => $this->sqlStatement->getSqlString(),
                "data" => $data
            ]);
            $hydrated = $this->hydrate($res);
            $this->currentAssociated->clear();
            /* TESTING */// echo $sql;
            /* TESTING */ //echo json_encode($res);
            return $hydrated; 
            //return $this->result($res);
        }
        if ($this->action === "insert")
        {
            $isDone = true; //CHECK
            $this->queryDb->transaction();
            foreach ($this->sqlStatementStack as $sql)
            {
                $isDone = $this->queryDb->insert((object)[
                    "sql" => $sql[0],
                    "data" => $sql[1]
                ]);
            }
            if ($isDone)
                {
                    $this->sqlStatementStack = [];
                    $this->queryDb->commit(); //IMPORTANT - IF ONE OF QUERY FAILS, ALL WILL BE FAILED
                }
                else {
                    $this->queryDb->rollback();
                }
            return $isDone;     
        }

        if ($this->action === "update") {            
            return $this->queryDb->batchWrite($this->action,$this->sqlStatementStack);
        }
    }

    /**
     * execute raw sql statements directly
     * instead of using this ORM
     * 
     * @param string $sql sql query string
     * @param object $data data to be bound on the prepared query string
     * @return mixed
      */
    public function rawSql ($action,$sql,$data)
    {        
    }
 }

?>