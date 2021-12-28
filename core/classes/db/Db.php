<?php

abstract class Db
{
    /** @var string Server (eg. localhost) */
    protected $_server;

    /** @var string Database user (eg. root) */
    protected $_user;

    /** @var string Database password (eg. can be empty !) */
    protected $_password;

    /** @var string Database type (MySQL, PgSQL) */
    protected $_type;

    /** @var string Database name */
    protected $_database;

    /** @var mixed Ressource link */
    protected $_link;

    /** @var mixed SQL cached result */
    protected $_result;

    /** @var mixed ? */
    protected static $_db;

    /** @var mixed Object instance for singleton */
    private static $_instance;



    public function getLink()
    {
        return $this->_link;
    }

    public function getMysqlColumns($table)
    {

        $tab = array();
        $rows = $this->s('SHOW COLUMNS FROM ' . $table);
        if ($rows) {
            foreach ($rows as $key => $row) $tab[] = $row['Field'];
        }
            return $tab;
    }

    /**
     * Get Db object instance (Singleton)
     *
     * @return object Db instance
     */
    public static function getInstance()
    {

        if (!isset(self::$_instance))
            self::$_instance = new PDOAb();
        return self::$_instance;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Build a Db object
     */
    public function __construct()
    {
     $gl_config = Tools::getConfig();


        $this->_server = $gl_config['database_master']['params']['host'];
        $this->_user = $gl_config['database_master']['params']['username'];
        $this->_password = $gl_config['database_master']['params']['password'];
        $this->_type = $gl_config['database_master']['adapter'];
        $this->_database = $gl_config['database_master']['params']['dbname'];
        $this->connect();
    }

    /**
     * Filter SQL query within a blacklist
     *
     * @param string $table Table where insert/update data
     * @param string $values Data to insert/update
     * @param string $type INSERT or UPDATE
     * @param string $where WHERE clause, only for UPDATE (optional)
     * @param string $limit LIMIT clause (optional)
     * @return mixed|boolean SQL query result
     */
    public function autoExecute($table, $values, $type, $where = false, $psql = true)
    {

        $params = [];
        if (strtoupper($type) == 'INSERT') {
            $query = 'INSERT INTO `' . $table . '` (';
            foreach ($values AS $key => $value) {
                $query .= '`' . $key . '`,';
            }
            $query = rtrim($query, ',') . ') VALUES (';
            foreach ($values AS $key => $value) {
                $query .= " :" . $key . ",";
                $params[":" . $key ] = $value;
            }
            $query = rtrim($query, ',') . ')';
            return $this->q($query, $params);
        } elseif (strtoupper($type) == 'UPDATE') {
            $query = 'UPDATE `' . ($table) . '` SET ';
            foreach ($values AS $key => $value) {
                $query .= '`' . $key . "`=". ":" . $key . ",";
                $params[":" . $key ] = $value;
                //   $query .= '`' . $key . '` = \'' . ($psql ? Tools::pSQL($value) : $value) . '\',';
            }
            $query = rtrim($query, ',');
            if ($where) {
                $query .= ' WHERE ' . $where;
            }
           return $this->q($query, $params);
        }
        return false;
    }


    /*********************************************************
     * ABSTRACT METHODS
     *********************************************************/

    /**
     * Open a connection
     */
    abstract public function connect();

    /**
     * Get the ID generated from the previous INSERT operation
     */
    abstract public function insertID();


    /**
     * Delete
     */
    abstract public function delete($table, $where = false, $limit = false);

    /**
     * Fetches a row from a result set
     */
    abstract public function execute($query);

    /**
     * Fetches an array containing all of the rows from a result set
     */
    abstract public function executeS($query, $array = true);

    /*
    * Get next row for a query which doesn't return an array
    */
    abstract public function nextRow($result = false);

    /**
     * Alias of Db::getInstance()->ExecuteS
     *
     * @acces string query The query to execute
     * @return array Array of line returned by MySQL
     */
    static public function s($query)
    {
        return Db::getInstance()->executeS($query);
    }

    static public function ps($query)
    {
        $ret = Db::s($query);
        p($ret);
        return $ret;
    }



    /**
     * Get Row and get value
     */
    abstract public function getRow($query);

    abstract public function getValue($query);

    /**
     * Returns the text of the error message from previous database operation
     */
    abstract public function getMsgError();


}
