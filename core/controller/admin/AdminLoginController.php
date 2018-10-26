<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminLoginController extends AdminController
{
    protected $need_to_be_log = false;

    public function index()
    {
        echo $this->viewManager->render("login.html");
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
            echo $this->twig->render('pages/login.html', array(

                'email'=>$email,
                'no-login-succes'=>1

            ));
        }
    }
}