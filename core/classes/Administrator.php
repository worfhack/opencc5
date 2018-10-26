<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 07/08/17
 * Time: 18:17
 */
class Administrator extends ObjectModel
{
    public $table = 'administrator';
    public $identifier = 'id_administrator';



    public $mail;

    public $password;

    public $firstname;

    public $lastname;




    public $active = 1;


    public static function isLogged()
    {
        if (isset($_SESSION['id_administrator'])) {
            return true;
        } else {
            return false;
        }

    }

    public static function getAdministrator()
    {
        if (isset($_SESSION['id_administrator']))
        {
            $admin = new Administrator($_SESSION['id_administrator']);
            $admin->picture_min = $admin->getPicture(true);
            $admin->picture_full = $admin->getPicture();

            return $admin;
        }else
        {
            return false;
        }

    }



    public function add($edit = false)
    {
        if(!$edit) {
            $this->password = Tools::encrypt($this->password);
        }
        //Purge de memcached

        return parent::add();
    }


    /**
     * @param $mail
     * @param $password
     * @return mixed
     */
    //Check si un customer existe déjà. (Avant la création de son compte et donc le lancement de la method add)
    static public function login($mail, $password)
    {
        $sql = 'SELECT e.id_administrator FROM `'._DB_PREFIX_.'administrator` e WHERE e.mail = \''.pSQL($mail).'\' AND e.password = \''.Tools::encrypt($password).'\'' ;
        return Db::getInstance()->getValue($sql);
    }

    static public function getName($idEmployee)
    {
        $sql = "SELECT firstname, lastname FROM "._DB_PREFIX_."administrator WHERE id_administrator=".$idEmployee;
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * @return boolean
     * @deprecated
     */
    static public function emailExist($email)
    {
        return Db::getInstance()->getValue('SELECT e.id_administrator FROM `' . _DB_PREFIX_ . 'administrator` e WHERE e.mail = \'' . pSQL($email) . '\'',
            $memcached = false);
    }


    /**
     * @return mixed
     * @deprecated
     */
    static public function getEmployees($id_employee_group = false)
    {
        $sql ='
            SELECT e.*
            FROM `'._DB_PREFIX_.'administrator` e';
        return Db::getInstance()->ExecuteS($sql, $array = true, $memcached = false);
    }

    public function getPicture($thumb = false)
    {
        if ($thumb == true) {

            $file = AVATAR_DIR . '/' . $this->id . '/' . $this->id . "-min.jpg";
            $url = _AVATAR_BASE_URL_ . '/' . $this->id . '/' . $this->id . "-min.jpg";
        } else {
            $file = AVATAR_DIR . '/HD/' . $this->id . ".jpg";
            $url = _AVATAR_BASE_URL_ . '/HD/' . $this->id . ".jpg";
        }

        if (file_exists($file)) {
            return $url;
        }
    }

    static public function getCleanAdmin($adminEdit = false)
    {
        if (empty($_POST['mail']) || empty($_POST['firstname']) || empty($_POST['lastname'])) {
            return false;
        }

        if(empty($_POST['password']) && !$adminEdit) {
            return false;
        }

        if(!isset($_POST['active']) || empty($_POST['active'])) {
            $_POST['active'] = false;
        }
        else {
            $_POST['active'] = true;
        }

        $admin = new Administrator();
        $admin->mail = filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL);
        $admin->firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $admin->lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        // $employee->phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        if(!$adminEdit) {
            $admin->password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
        } else {
            $admin->password = $adminEdit->password;
        }
        $admin->active = $_POST['active'];

        return $admin;
    }

    public function changePassword($newPass) {
        $this->password = $newPass;
        $this->add();
    }





}