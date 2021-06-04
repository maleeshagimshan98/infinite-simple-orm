# infinite-orm

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

* Configure a web server to serve the project folder.
* Configure MySQL (MariaDB) database.
* Import the database configuration provided in **./db** folder
* Edit `pdoConnection.php`, in root folder, change your
  * host name,
  * user name and password as shown below
* This ORM is currently works only with a **single database**.

```php
class pdoConnection  {

    private $username = "YOUR_USER_NAME"; // change this to your username
    private $password = "YOUR_DATABASE_PASSWORD";    // change this to your password
    private $dsn = "mysql:host=HOST;dbname=test_data_mapper"; // change HOST to your hostname

```

* Use Postman or similar software to hit `test.php`, and the database query results will be shown in response.



### Database Configuration

* Find the database configuration in **./config/entity_definition.json** file.

* configure it as you need, using guide given below

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
* **Entity Name is the database table name**
* *Entity attributes* are described in the following formats :
```
{
    "ENTITY_ATTRIBUTE" : "TABLE_COLUMN"
}
````
* Primary keys can be defined like this, 
```php

    "ENTITY_ATTRIBUTE_2" : {"PRIMARY" : TRUE}

    },
}
```

* Define Entity's *Associations*, if any,

```php
{
 "ENTITY_ATTRIBUTE" : "TABLE_COLUMN",
  "_assoc" : {
                "ASSOCIATED_ENTITY" : {
                    "TARGET" : "TABLE_NAME OF TARGET ENTITY",
                    "REFER" : "REFFERED COLUMN OF THAT TABLE",
                    "INVERSE" : "PARENT ENTITY'S CORRESPONDING ATTRIBUTE (FOREIGN KEY OR PRIMARY KEY)"
                    "TYPE" : "OneToMany"
                 }
              }
  }

```

### Getting data from database

* To get data from the database, first you need to get an `$EntityManager` instance, then call `get(entity_name)`

```php
$data = $EntityManager->get('entity_name')->go();

or 

// get data by a key

$data = $EntityManager->get('entity_name',['entity_attrib','some_value'])->go('some_value');

//invoke go() to get the data;
```

### Inserting data to database

```php

//get an EntityResult instance (Not an Entity instance)
$product = $entityManager->entity("product");

// inserting values
$props = ["id" => "llmp_010", "product_name" => "test_product", "img_url" => "some_url"];

$entityManager->save($product,$props);
$entityManager->go();

//invoke go() to save the data;

```




## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.



## License
[MIT](https://choosealicense.com/licenses/mit/)
