<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 07/08/17
 * Time: 18:17
 */
class Users extends ObjectModel
{
    public $table = 'users';
    public $identifier = 'id_user';


    public $email;
    public $password;
    public $newpassword;
    public $firstname;
    public $lastname;
    public $id_user;


    public function update()
    {
        if ($this->newpassword) {
            $this->password = Tools::encrypt($this->newpassword);
        }
        return parent::update();
    }


    public static function isLogged()
    {
        if (isset($_SESSION['id_user'])) {
            return true;
        } else {
            return false;
        }

    }

    public static function getUsers()
    {
        if (isset($_SESSION['id_user'])) {
            $admin = new Users($_SESSION['id_user']);
            $admin->picture_min = $admin->getPicture(true);
            $admin->picture_full = $admin->getPicture();

            return $admin;
        } else {
            return false;
        }

    }


    public function add($edit = false)
    {
        if (!$edit) {
            $this->password = Tools::encrypt($this->password);
        }
        if ($this->newpassword) {
            $this->password = Tools::encrypt($this->newpassword);
        }
        //Purge de memcached

        return parent::add();
    }


    /**
     * @param $mail
     * @param $password
     * @return mixed
     */
    static public function login($mail, $password)
    {
        $sql = 'SELECT e.id_user as id_user, e.password as password FROM `' . _DB_PREFIX_ . 'users` e WHERE e.email = \'' . Tools::pSQL($mail) . '\' ';

        $user = Db::getInstance()->getRow($sql);
        if (!$user) {
            return false;
        }


        $hash = $user['password'];
        $test = password_verify($password, $hash);
        if (!$test) {
            return false;
        }
        return $user['id_user'];

    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    static public function getName($id_user)
    {
        $sql = "SELECT firstname, lastname FROM " . _DB_PREFIX_ . "users WHERE id_user=" . $id_user;
        return Db::getInstance()->ExecuteS($sql);
    }

    static public function emailExist($email)
    {
        return Db::getInstance()->getValue('SELECT e.id_user FROM `' . _DB_PREFIX_ . 'users` e WHERE e.email = \'' . Tools::pSQL($email) . '\'',
            $memcached = false);
    }


    /**
     * @return mixed
     * @deprecated
     */
    static public function getEmployees($id_employee_group = false)
    {
        $sql = '
            SELECT e.*
            FROM `' . _DB_PREFIX_ . 'administrator` e';
        return Db::getInstance()->ExecuteS($sql, $array = true, $memcached = false);
    }

    public function getPicture($thumb = false)
    {
        return '';
//        if ($thumb == true) {
//
//            $file = AVATAR_DIR . '/' . $this->id . '/' . $this->id . "-min.jpg";
//            $url = _AVATAR_BASE_URL_ . '/' . $this->id . '/' . $this->id . "-min.jpg";
//        } else {
//            $file = AVATAR_DIR . '/HD/' . $this->id . ".jpg";
//            $url = _AVATAR_BASE_URL_ . '/HD/' . $this->id . ".jpg";
//        }
//
//        if (file_exists($file)) {
//            return $url;
//        }
    }

    static public function CreateUserFromForm(&$error)
    {
        if (empty($_POST['email']) || empty($_POST['firstname']) || empty($_POST['lastname'])
            || empty($_POST['password'])
            || empty($_POST['password2'])

        ) {
            $error = 'Required field empty';
            return false;
        }




        $user = new Users();
        $user->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $user->firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $user->lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        // $employee->phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $user->password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
        $password2 = filter_input(INPUT_POST, 'password2', FILTER_DEFAULT);
        if ($password2 != $user->password)
        {
            $error = 'password verification faild';
            return false;
        }
        if (!Validate::isPasswd($user->password))
        {
            $error = 'your password not success security ';
            return false;
        }
        if (Users::emailExist($user->email))
        {
            $error = 'this email already exist';
            return false;
        }
        return $user;
    }
    public function saveLinkResetPassword($link)
    {

        $max_link_date_sec = 3600;
        $max_link_date = new DateTime();
        $max_link_date->modify('+ ' . $max_link_date_sec . ' second');

        Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'user_password_reset',
            array('id_user' => intval($this->id),
                'link' => $link,
                'validate' => 1,
                'end_validate' => $max_link_date->format('Y-m-d H:i:s'),
            ), 'INSERT', $where = false, $psql = true);

    }
    static public function resetLinkExist($link)
    {
        return ObjectModel::getSingleInfo($table = 'user_password_reset', $where_row = 'link',
            $where_value = $link, $row = 'id_user_password_reset', $memcached = false);
    }
    static public function getUserIdFromLink($link)
    {
        // TimeZone
        $TZ = date('P');

        $sql = "select id_user  FROM  `" . _DB_PREFIX_ . "user_password_reset` " .
            " WHERE link = '$link'  AND validate= 1 AND  end_validate >=  CONVERT_TZ(NOW(), @@session.time_zone, '$TZ')";
        $row = Db::getInstance()->getRow($sql, true, false);
        if (isset($row['id_user'])) {
            return $row['id_user'];
        }
        return false;

    }
    public function genereateResetPasswordLink()
    {

            do {
                $link = bin2hex(random_bytes(30));
            } while (Users::resetLinkExist($link));

            $this->saveLinkResetPassword($link);
            return $link;
    }
    public function changePassword($newPass)
    {
        $this->newpassword = $newPass;
        $this->add();
    }


}