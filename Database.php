<?php
/**
 * Author: skonealone
 * Date: 12/13/16
 * Time: 12:21 PM
 * Email: skonealone@gmail.com 
 * Ref URL: http://php.net/manual/en/book.pdo.php
 */

/**
 * Database config file
 */
require_once(__DIR__ . "/configs/db.conf");


/**
 * Mysql database class - only one connection alowed
 */
class Database {
    private static $_instance; //The single instance
    private $_connection;
    private $_host          = $DB_HOST;
    private $_username      = $DB_USER;
    private $_password      = $DB_PASSWORD;
    private $_database      = $DB_NAME;
    private $_port          = $DB_PORT;
    private $_charset       = $DB_CHARSET;

    /*
    Get an instance of the Database
    @return Instance
    */
    public static function getInstance() {
        if(!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Constructor
    private function __construct() {

        if(!empty($this->_host) && !empty($this->_username) && !empty($this->_password) && !empty($this->_database)) {

            try {
                //$this->_connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database, $this->_port);
                /**
                 * PDO::FETCH_NUM returns enumerated array
                 * PDO::FETCH_ASSOC returns associative array
                 * PDO::FETCH_BOTH - both of the above
                 * PDO::FETCH_OBJ returns object
                 * PDO::FETCH_LAZY allows all three (numeric associative and object) methods without memory overhead.
                 */
                $opt = array(
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES   => false,
                        );
                $this->_connection  = new \PDO("mysql:host=$this->_host;dbname=$this->_database;port=$this->_port;charset=$this->_charset", 
                                        $this->_username, $this->_password);

                // Error handling
                //if(mysqli_connect_error()) {
                //    throw new Exception("Failed to connect to MySQL: " . mysql_connect_error(). " (" .  mysqli_connect_errno() . ")");
                //}

                if ($this->_charset) {
                    $this->_connection->set_charset($this->_charset);
                }
            }
            catch (PDOException $e) {
                die($e->getMessage());
            }
        }
        else {
            trigger_error("Empty database credentails!!" , E_USER_ERROR);
        }
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone() { }

    // Get mysqli connection
    public function getConnection() {
        return $this->_connection;
    }

}
/**
The Database class will provide all PDO object so that you can use the PDO functions/methods
Example:
$db = Database::getInstance();
$rs = $db->getConnection();

$sql = "SELECT * FROM table_name_here";
foreach ($rs->query($sql) as $row) {
    var_dump($row);
}
**/
