<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 07/08/17
 * Time: 17:40
 */
class AdminLoginController extends  AdminController
{
    protected $need_to_be_log = false;
    protected $pageName = 'admin_login';


    public function form()
    {
        echo $this->viewManager->render("pages/login.html");

    }

    public function logout()
    {
        $_SESSION['id_administrator'] = 0;
        unset(  $_SESSION['id_administrator']);
        Tools::redirect(_BASE_URL_.'/' . _ADMIN_URI_);
    }
    public function singin()
    {
        $email = Tools::getValue('email');
        $password = Tools::getValue('password');
        $id_admin = Administrator::login($email, $password);
        if ($id_admin)
        {
            $username = Administrator::getName($id_admin);
            $username = array_shift($username);
            $username = $username['firstname'] . " " . $username['lastname'];
            $_SESSION['id_administrator'] = $id_admin;
            $_SESSION['username'] = $username;
            Tools::redirect(_BASE_URL_.'/' . _ADMIN_URI_);
        }else
        {
            echo $this->viewManager->render('pages/login.html');
        }
    }
    public function index()
    {


            echo $this->viewManager->render("pages/login.html");


    }
}
