<?php
/**
* © Maleesha Gimshan - 2021 - github.com/maleeshagimshan98
* database connection singleton
*/

declare(strict_types = 1);

namespace Infinite\SimpleOrm;

/**
 * database connection class
 */
class DbConnection  {

    private static $connection;

    public function __construct () {        
    }

    /**
     * create data source name according to the database driver and database
     *
     * @param string $host - host server
     * @param string $driver - database driver (mysql, postgres, oracle)
     * @param string $dbName - name of database
     * @return string - created dsn (data source name) string
     */
    private static function createDsn (string $host, string $driver, string $dbName) : string
    {
        switch ($driver) {
            case 'mysql' :
                return "mysql:host=$host;dbname=$dbName";
            break;
        }        
    }

    /**
     * connect to the database
     *
     * @param array $config
     * @return void
     * @throws \Exception
     */
    private static function connect (array $config) : void
    {
        $dsn = self::createDsn($config['host'],$config['driver'],$config['db']);
        try {
            self::$connection = new \PDO($dsn, $config['username'], $config['password']);
            self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch (\PDOException $e) {
            //echo $e->getMessage();
            throw new \Exception("Database_Connection_Failed");
        }    
    }

    /**
     * get the database connection
     *
     * @param array $config
     * @return \PDO
     * @throws \Exception
     */
    public static function getConnection (array $config = []) : \PDO
    {
        if (self::$connection) {
            return self::$connection;
        }
        elseif (count($config) == 0) {
            throw new \Exception("No configuration data found. pass an array of configuration data to connect to the database");
        }
        else {
            self::connect($config);
            return self::$connection;
        }
    }
        
}
?>