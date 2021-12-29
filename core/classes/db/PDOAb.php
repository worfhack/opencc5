<?php

define ('_PS_DEBUG_' , false);

class PDOAb extends Db
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
        try {
            $this->_link = new PDO('mysql:host=' . $this->_server . ';dbname=' . $this->_database . ';charset=utf8', $this->_user, $this->_password);
        }
        catch (Exception $e){

            throw New CoreException('Link to database cannot be established.', 500);
        }

        /* Disable some MySQL limitations */
       // mysqli_query ($this->_link, 'SET GLOBAL SQL_MODE=\'\'' );

        $recipesStatement = $this->_link->prepare('SET GLOBAL SQL_MODE=\'\'');
        $recipesStatement->execute();
        return $this->_link;
    }



    public function disconnect ()
    {
        $this->_link = false;
    }

    public function getRow ($query , $params=[])
    {
        self::sanitizeQuery ($query);

        $this->_result = false;
        $query = $query . ' LIMIT 1';
            if ( $this->_link){
                $recipesStatement = $this->_link->prepare($query);
                $recipesStatement->execute($params);
                $this->_result = $recipesStatement->fetch(PDO::FETCH_ASSOC);
                $this->displayMySQLError ($query);
                return $this->_result;
            }
        $this->displayMySQLError ($query);
        return false;
    }

    public function getValue ($query , $params=[])
    {

        self::sanitizeQuery ($query);

        if ( $this->isAdmin () ) {
        }

        $this->_result = false;
        $query = $query . ' LIMIT 1';
        if ( $this->_link) {
            // if ( $this->_link && ($this->_result = mysqli_query ($this->_link, $query )) && is_array ($tmpArray = mysqli_fetch_assoc ($this->_result)) ) {
            $recipesStatement = $this->_link->prepare($query);

            $recipesStatement->execute($params);
            $this->_result = $recipesStatement->fetch(PDO::FETCH_ASSOC);

            if ($this->_result)
            $this->_result = array_shift(  $this->_result );
            return $this->_result;
        }
            else {
                return false;
            }
        return false;
    }

    public function truncate ($table)
    {
        $query = 'TRUNCATE ' . _DB_PREFIX_ . $table;
        if ( $this->_link ) {
            $recipesStatement = $this->_link->prepare($query);
            $recipesStatement->execute();
            $this->displayMySQLError ($query);
        }

    }

    public function execute ($query)
    {
        self::sanitizeQuery ($query);
        $this->_result = false;
        if ( $this->_link ) {
            $recipesStatement = $this->_link->prepare($query);
            $recipesStatement->execute();
            return true;
        }
        $this->displayMySQLError ($query);
        return false;
    }

    public function executeS ($query , $array = true , $params=[])
    {
        self::sanitizeQuery ($query);


            $this->_result = false;
            if ( $this->_link){
                //&& ($this->_result = mysqli_query ($this->_link, $query )) ) {
                //}

                $recipesStatement = $this->_link->prepare($query);
                $recipesStatement->execute($params);
                $this->_result = $recipesStatement->fetchAll();

                $this->displayMySQLError ($query);
                if ( !$array ) {
                    return $this->_result;
                }
                $resultArray = [];
                foreach ( $this->_result as $row){

//                while ($row = mysqli_fetch_assoc ($this->_result)) {
                    $resultArray[] = $row;
                }

                return $resultArray;
            }
        return false;
    }

    public function nextRow ($result = false)
    {
        return mysqli_fetch_assoc ($result ? $result : $this->_result);
    }

    public function delete ($table , $where = false , $limit = false)
    {

    }



    public function insertID ()
    {
        if ( $this->_link ) {
            return ($this->_link->lastInsertId());
        }
        return false;
    }


    protected function q ($query, $params=[])
    {
        self::sanitizeQuery ($query);
        $this->_result = false;
        if ( $this->_link ) {
            $recipesStatement = $this->_link->prepare($query);
            return $recipesStatement->execute($params);
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



    }

    static public function tryToConnect ($server , $user , $pwd , $db)
    {
        if ( !$link = mysqli_connect ($server , $user , $pwd) ) {
            return 1;
        }
        if ( !mysqli_select_db ($link , $db) ) {
            return 2;
        }
        mysqli_close  ($link);
        return 0;
    }

    static public function tryUTF8 ($server , $user , $pwd)
    {
        $link = mysqli_connect ($server , $user , $pwd);
        if ( !mysqli_query ($link, 'SET NAMES \'utf8\'' ) ) {
            $ret = false;
        }
        else {
            $ret = true;
        }
        mysqli_close ($link);
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
                    $match_len = strlen ($matche);
                    for ($i = 0; $i <$match_len;  $i++) $explode_matche .= $matche[$i] . ' ';
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

            throw new Exception('Tentative de piratage');
        }
        return true;
    }
}
