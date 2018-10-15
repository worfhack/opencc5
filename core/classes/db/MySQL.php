<?php

define ('_PS_DEBUG_' , false);

class MySQL extends Db
{
    public function isAdmin ()
    {
        if ( (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] && strstr ($_SERVER['HTTP_HOST'] , 'bo.')) || (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] && strstr ($_SERVER['REQUEST_URI'] , 'admin_')) ) {
            return true;
        }
        return false;
    }

    public function connect ()
    {


        if ( $this->_link = mysqli_connect ($this->_server , $this->_user , $this->_password) ) {
            if ( !$this->set_db ($this->_database) ) {
                throw New CoreException('The database selection cannot be made.', 500);
            }
        }
        else {
            throw New CoreException('Link to database cannot be established.', 500);


        }
        /* UTF-8 support */
        if ( !mysqli_query ($this->_link, 'SET NAMES \'utf8\'' ) ) {

            throw New CoreException('Fatal error: no utf-8 support. Please check your server configuration.', 500);
        }
        /* Disable some MySQL limitations */
        mysqli_query ($this->_link, 'SET GLOBAL SQL_MODE=\'\'' );
        return $this->_link;
    }

    /* do not remove, useful for some modules */
    public function set_db ($db_name)
    {
        return mysqli_select_db ($this->_link , $db_name);
    }

    public function disconnect ()
    {
        if ( $this->_link ) {
            @mysqli_close ($this->_link);
        }
        $this->_link = false;
    }

    public function getRow ($query , $memcached = false)
    {
        self::sanitizeQuery ($query);
        if ( $this->isAdmin () ) {
            $memcached = false;
        }
        $this->_result = false;
        $query = $query . ' LIMIT 1';
        $memcached_key = md5 ($this->_database . $query);

        if ( $memcached && $this->_result = self::MemCached ()->get ($memcached_key) ) {


            return $this->_result;
        }
        else {
            if ( $this->_link AND $this->_result = mysqli_query ($this->_link, $query) ) {
                $this->_result = mysqli_fetch_assoc ($this->_result);
                if ( $memcached ) {
                    self::MemCached ()->set ($memcached_key , $this->_result , 3600);
                }
                $this->displayMySQLError ($query);
                return $this->_result;
            }
        }
        $this->displayMySQLError ($query);
        return false;
    }

    public function getValue ($query , $memcached = false)
    {
        /*if ($memcached == false)
        echo ($query) .  "<br><br><br>memcache est $memcached";*/
        self::sanitizeQuery ($query);

        if ( $this->isAdmin () ) {
            $memcached = false;
        }

        $this->_result = false;
        $query = $query . ' LIMIT 1';
        $memcached_key = md5 ($this->_database . $query);

        if ( $memcached && $this->_result = self::MemCached ()->get ($memcached_key) ) {
            return $this->_result;
        }
        else {
            if ( $this->_link AND $this->_result = mysqli_query ($this->_link, $query ) AND is_array ($tmpArray = mysqli_fetch_assoc ($this->_result)) ) {
                $this->_result = array_shift ($tmpArray);
                if ( $memcached ) {
                    self::MemCached ()->set ($memcached_key , $this->_result , 3600);
                }
                return $this->_result;
            }
            else {
                return false;
            }
        }
        return false;

        // $this->_result = false;
        // if ($this->_link AND $this->_result = mysql_query($query.' LIMIT 1', $this->_link) AND is_array($tmpArray = mysqli_fetch_assoc($this->_result)))
        // return array_shift($tmpArray);
        // return false;
    }

    public function truncate ($table)
    {
        $query = 'TRUNCATE ' . _DB_PREFIX_ . $table;
        if ( $this->_link ) {
            $this->_result = mysqli_query ($this->_link, $query );
            $this->displayMySQLError ($query);
            return $this->_result;
        }

    }

    public function Execute ($query)
    {
        self::sanitizeQuery ($query);
        //p($query);
        $this->_result = false;
        if ( $this->_link ) {
            $this->_result = mysqli_query ($this->_link, $query);
            $this->displayMySQLError ($query);
            return $this->_result;
        }
        $this->displayMySQLError ($query);
        return false;
    }

    public function ExecuteS ($query , $array = true , $memcached = false)
    {
        //p($query);
        self::sanitizeQuery ($query);

        if ( $this->isAdmin () ) {
            $memcached = false;
        }

        $memcached_key = md5 ($this->_database . $query);

        if ( $memcached && $this->_result = self::MemCached ()->get ($memcached_key) ) {
            return $this->_result;
        }
        else {
            $this->_result = false;
            if ( $this->_link && $this->_result = mysqli_query ($this->_link, $query ) ) {
                $this->displayMySQLError ($query);
                if ( !$array ) {
                    return $this->_result;
                }
                $resultArray = [];

                while ($row = mysqli_fetch_assoc ($this->_result)) {
                    $resultArray[] = $row;
                }

                if ( $memcached ) {
                    self::MemCached ()->set ($memcached_key , $resultArray , 1800);
                }
                return $resultArray;
            }
        }
        $this->displayMySQLError ($query);
        return false;
    }

    public function nextRow ($result = false)
    {
        return mysqli_fetch_assoc ($result ? $result : $this->_result);
    }

    public function delete ($table , $where = false , $limit = false)
    {
        self::sanitizeQuery ($query);

        $this->_result = false;
        if ( $this->_link ) {
            return mysqli_query ($this->_link, 'DELETE FROM `' . pSQL ($table) . '`' . ($where ? ' WHERE ' . $where : '') . ($limit ? ' LIMIT ' . intval ($limit) : '') );
        }
        return false;
    }

    public function NumRows ()
    {
        if ( $this->_link AND $this->_result ) {
            return mysqli_num_rows ($this->_result);
        }
    }

    public function Insert_ID ()
    {
        if ( $this->_link ) {
            return mysqli_insert_id ($this->_link);
        }
        return false;
    }

    public function Affected_Rows ()
    {
        if ( $this->_link ) {
            return mysqli_affected_rows ($this->_link);
        }
        return false;
    }

    protected function q ($query)
    {
        self::sanitizeQuery ($query);
        //p($query);
        $this->_result = false;
        if ( $this->_link ) {
            return mysqli_query ($this->_link, $query );
        }
        return false;
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     *
     * @acces public
     * @return string error
     */
    public function getMsgError ($query = false)
    {
        return mysqli_error ($this->_link);
    }

    public function getNumberError ()
    {
        return mysqli_errno  ($this->_link);
    }

    public function displayMySQLError ($query = false)
    {

        if ( _PS_DEBUG_ AND mysqli_errno  ($this->_link) ) {
            if ( $query ) {
                die(Tools::displayError (mysqli_error ($this->_link) . '<br /><br /><pre>' . $query . '</pre>'));
            }
            die(Tools::displayError ((mysqli_error ($this->_link))));
        }
    }

    static public function tryToConnect ($server , $user , $pwd , $db)
    {
        if ( !$link = @@mysqli_connect ($server , $user , $pwd) ) {
            return 1;
        }
        if ( !@mysqli_select_db ($link , $db) ) {
            return 2;
        }
        @mysqli_close  ($link);
        return 0;
    }

    static public function tryUTF8 ($server , $user , $pwd)
    {
        $link = @mysqli_connect ($server , $user , $pwd);
        if ( !mysqli_query ($link, 'SET NAMES \'utf8\'' ) ) {
            $ret = false;
        }
        else {
            $ret = true;
        }
        @mysqli_close ($link);
        return $ret;
    }

    // Applique un filtre sur les requetes
    static public function sanitizeQuery ($query)
    {
        $query = str_replace (chr (0) , '' , $query);  // Replace NullByte
        $patterns = [// Sleep
            '/sleep\(\)/' , // Sleep
            '/sleep\(\d+\)/' , // Sleep avec INT
            '/SLEEP\(\d+\)/' , // Sleep UPPERCASE avec INT
            '/sleep\(\d+\.\d+\)/' , // Sleep avec float
            '/SLEEP\(\d+\.\d+\)/' , // Sleep UPPERCASE avec float

            // Truncat
            '/truncat/' ,
            '/TRUNCATE/' ,

            // Escate + diese
            //'/\'\#/',
        ];

        $preg_match = false;
        $preg_match_patterns = [];
        foreach ($patterns as $pattern) {
            if ( preg_match ($pattern , $query , $matches) ) {
                $preg_match = true;
                foreach ($matches as $matche) {
                    $query = str_replace ($matche , '[-- Sanitized String --]' , $query);

                    $explode_matche = '';
                    for ($i = 0; $i < strlen ($matche); $i++) $explode_matche .= $matche[$i] . ' ';
                    $preg_match_patterns[] = $explode_matche;
                }
            }
        }

        if ( $preg_match ) {


            $SqlAttack = new SqlAttack();
            $SqlAttack->ip = Tools::getRemoteAddress ();
            $SqlAttack->query = $query;
            $SqlAttack->pattern = implode (', ' , $preg_match_patterns);
            $SqlAttack->add ();

            die('Tentative de piratage');
        }
        // }
        return true;
    }
}