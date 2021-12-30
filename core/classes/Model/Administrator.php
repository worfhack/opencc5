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
    public $newpassword;
    public $firstname;

    public $lastname;
    public $id_administrator;


    public $active = 1;

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


    static public function getAdministratorIdFromLink($link)
    {
        // TimeZone
        $TZ = date('P');

        $sql = "select id_administrator  FROM  `" . _DB_PREFIX_ . "administrator_password_reset` " .
            " WHERE link = '$link'  AND validate= 1 AND  end_validate >=  CONVERT_TZ(NOW(), @@session.time_zone, '$TZ')";
        $row = Db::getInstance()->getRow($sql);
        if (isset($row['id_administrator'])) {
            return $row['id_administrator'];
        }
        return false;

    }


    public function update()
    {
        if ($this->newpassword) {
            $this->password = Tools::encrypt($this->newpassword);
        }
        return parent::update();
    }


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
        if (isset($_SESSION['id_administrator'])) {
            $admin = new Administrator($_SESSION['id_administrator']);
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
        $sql = 'SELECT e.id_administrator as id_administrator, e.password as password FROM `' . _DB_PREFIX_ . 'administrator` e WHERE e.mail = :email ';
        $admin = Db::getInstance()->getRow($sql, array(":email"=>$mail));
        if (!$admin) {
            return false;
        }


        $hash = $admin['password'];
        $test = password_verify($password, $hash);
        if (!$test) {
            return false;
        }
        return $admin['id_administrator'];

    }

    static public function getName($idEmployee)
    {
        $sql = "SELECT firstname, lastname FROM " . _DB_PREFIX_ . "administrator WHERE id_administrator=" . $idEmployee;
        return Db::getInstance()->executeS($sql);
    }

    public function saveLinkResetPassword($link)
    {

        $max_link_date_sec = 3600;
        $max_link_date = new DateTime();
        $max_link_date->modify('+ ' . $max_link_date_sec . ' second');

        Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'administrator_password_reset',
            array('id_administrator' => intval($this->id),
                'link' => $link,
                'validate' => 1,
                'end_validate' => $max_link_date->format('Y-m-d H:i:s'),
            ), 'INSERT', false, true);

    }

    static public function emailExist($email)
    {
        return Db::getInstance()->getValue('SELECT e.id_administrator FROM `' . _DB_PREFIX_ . 'administrator` e WHERE e.mail = :email ',
            array(":email"=>$email));
    }

    static public function resetLinkExist($link)
    {
        return ObjectModel::getSingleInfo('administrator_password_reset', 'link',
            $link, 'id_administrator_password_reset');
    }

    public function genereateResetPasswordLink()
    {
        do {
            $link = bin2hex(random_bytes(30));
        } while (Administrator::resetLinkExist($link));

        $this->saveLinkResetPassword($link);
        return $link;
    }

    static public function getEmployees($id_employee_group = false)
    {
        $sql = '
            SELECT e.*
            FROM `' . _DB_PREFIX_ . 'administrator` e';
        return Db::getInstance()->executeS($sql, true);
    }


    static public function getCleanAdmin($adminEdit = false)
    {
        if (empty($_POST['mail']) || empty($_POST['firstname']) || empty($_POST['lastname'])) {
            return false;
        }

        if (empty($_POST['password']) && !$adminEdit) {
            return false;
        }

        if (!isset($_POST['active']) || empty($_POST['active'])) {
            $_POST['active'] = false;
        } else {
            $_POST['active'] = true;
        }

        $admin = new Administrator();
        $admin->mail = filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL);
        $admin->firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $admin->lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        if (!$adminEdit) {
            $admin->password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
        } else {
            $admin->password = $adminEdit->password;
        }
        $admin->active = $_POST['active'];

        return $admin;
    }

    public function changePassword($newPass)
    {
        $this->newpassword = $newPass;
        $this->add();
    }


}
