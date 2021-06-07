# infinite-simple-orm

![licence](https://img.shields.io/badge/licence-MIT-success)
![licence](https://img.shields.io/badge/PHP-v7.2-blue)

A simple object-relational mapping library built using PHP.

### Note

##### This project is intended only for experimental purposes, and in fact, the library is under development. It is not recommended for any usage on a production code.


## Installation
Simply download this repository from github.
Use composer to install dependencies.

```bash
composer install
```

## Usage

* Configure MySQL (MariaDB) database.
* Import the database configuration provided in **./db** folder to give a quick try
* Edit `pdoConnection.php`, in root folder, change
  * host name,
  * user name and password as shown below, or **pass your own `PDO` instance**.
* This ORM currently works only with a **single database**.

```php
class pdoConnection  {

    private $username = "YOUR_USER_NAME"; // change this to your username
    private $password = "YOUR_DATABASE_PASSWORD";    // change this to your password
    private $dsn = "mysql:host=HOST;dbname=test_data_mapper"; // change HOST to your hostname

```

* Use Postman or similar software to hit `test.php`, and the database query results will be shown in response.



### Database Configuration

* Find the database configuration in **./config/entity_definition.json** file.

* configure `entity_definition.json` as needed, using guide given below.

````php
{

  "product": {
        "id": {
            "name" : "id",
            "primary" : true
        },
        "_assoc" : {
            "product_sku" : {
                "target" : "product_sku",
                "refer" : "product_id",
                "inverse" : "id",
                "type" : "OneToMany"
            }
        }
        
    },
````
* **Entity Name is used as the database *table name* by default**. To explicitly set the database table name,
  
````php

// 'product' is used as table name by default

"product": {

        "__table_name" : "product_table"
        //if __table_name is explicitly defined, it will be used as the table name of entity
        
        // attributes 
},
````

* *Entity attributes* are described in the following formats :
```
{
    "entity_attribute" : "table_column"
    
    Or
    
    "entity_attribute" : {
                       "name" : "column_name"
                       //if explicitly defined, name will be used as the column name
    }
}
````
* *Primary keys* can be defined like this, (multiple attributes can be defined as primary - composite keys)
```php
{
    "entity_attribute_2" : {"primary" : TRUE}
}
```
* *Auto_Incremented* values

````php
"product" : {
     
       "entity_attribute" : {"autoIncrement" : TRUE}
     
}
````

* Define Entity's *Associations*, if any,

```php
{

 "entity_attribute" : "table_column",
 
  "_assoc" : {
                "associated_entity_name" : {
                    "target" : "associated entity_name",
                    "refer" : "referred attribute of associated entity", // how the entity joins the other entity
                    "inverse" : "PARENT ENTITY'S CORRESPONDING ATTRIBUTE (FOREIGN KEY OR PRIMARY KEY)"
                    "type" : "OneToMany"
                 }
   }
}

```

### Obtain EntityManager

```php
$connection = new pdoConnection();

$entityManager = new EntityManager ((object) [
    "connection" => $connection->connection()
]);
```

### Fetch data from database

* To fetch data from the database, obtain an `$EntityManager` instance, call `get(entity_name)`

```php

$data = $EntityManager->get('entity_name')->go();

or 

// get data by a key

//@return array 
$data = $EntityManager->get('entity_name',['entity_attrib' => 'some_value', 'entity_attrib_2' => 'some_other_value'])->go();

//invoke go() to get the data;
```

### Inserting data to database

* Once you retrieve an entity from database, EntityManager keeps track of it. If something has been changed, ```EntityManager```
takes care of changed properties and updates the database, once you save.

  * From last example, to change a property in the Entity and save,

````php

$data = $EntityManager->get('entity_name',['entity_attrib' => 'some_value', 'entity_attrib_2' => 'some_other_value'])->go();
$data_one = $data[0];

$data_one->some_attribute = "just_changed";
$entityManager->save($data_one);

//go() executes the query and do the insertion 
$entityManager->go();

````


```php

//get an EntityResult instance
$product = $entityManager->entity("product");

// inserting values
$props = (object) ["id" => "test_010", "product_name" => "test_product", "img_url" => "some_url"];

//no need to set all properties of entity manually, all the available properties for entities will be set from the object we pass

$entityManager->save($product,$props); 
$entityManager->go();

//invoke go() to save the data;

```




## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.



## License
[MIT](https://choosealicense.com/licenses/mit/)
