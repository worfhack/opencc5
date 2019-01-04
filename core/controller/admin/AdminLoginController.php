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
    public function resetpassword()
    {
        $context = Context::getContext();

        $link = Tools::getValue('link');
        if (!$link) {
            if (Tools::isSubmit("sendEmail")) {
                $email = Tools::getValue("email");
                $id_administrator = Administrator::emailExist($email);

                if ($id_administrator) {
                    $admin = new Administrator($id_administrator);
                    $link = $admin->genereateResetPasswordLink();
                    Mail::send($admin->mail, 'Your password reset link', 'resetpassword_admin', ['firstname' => $admin->getFirstname(),
                        'lastname' => $admin->getLastname(),
                        'link' => $context->getBaseurl() . _ADMIN_URI_ . '/resetpassword?link=' . $link
                    ]);
                    $this->viewManager->initVariable(

                        array('succes' => Tools::translate("reset link send by email"),

                        ));
                } else {
                    $this->viewManager->initVariable(

                        array('error' => Tools::translate("Not account found"),

                        ));
                }


            }
            echo $this->viewManager->render("pages/renewpasswordform.html");
        }else
        {

            if (!Tools::isSubmit("resetpassword")) {

                $id_administrator = Administrator::getAdministratorIdFromLink($link);
                if (!$id_administrator)
                {
                    $this->viewManager->initVariable(

                        array('error' => Tools::translate("Invalid Link"),

                        ));
                }else {
                    $this->viewManager->initVariable(

                        array('link' => Tools::translate($link),

                        ));
                }
                echo $this->viewManager->render("pages/renewpasswordform.html");
            }else
            {
                $id_administrator = Administrator::getAdministratorIdFromLink($link);
                if (!$id_administrator)
                {
                    $this->viewManager->initVariable(

                        array('error' => Tools::translate("Invalid Link"),

                        ));
                    echo $this->viewManager->render("pages/renewpasswordform.html");
                }else {

                    $error= '';
                    $admin = new Administrator($id_administrator);
                    $password = Tools::getValue("password");
                    $password2 = Tools::getValue("password2");
                    if ($password != $password2) {
                        $error = 'password verification faild';
                    }
                    if ($password) {
                        if (!Validate::isPasswd($password)) {
                            $error = 'your password not success security ';

                        }
                    }
                    if ($error) {
                        $this->viewManager->initVariable(

                            array('error' => Tools::translate($error),'link'=>$link

                            ));
                        echo $this->viewManager->render("pages/renewpasswordform.html");
                    } else {

                        $admin->newpassword = $password;
                        $admin->update();
                    }
                    Tools::redirect(_BASE_URL_.'/' . _ADMIN_URI_  . '/login');




                }
            }
        }

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
