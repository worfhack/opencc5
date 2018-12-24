<?php

class UserController extends FrontController
{
    protected $with_sidebar = false;

    public function register()
    {

        echo $this->viewManager->render("pages/register.html");


    }

    public function index()
    {
        $user = new Users($this->user->id_user, _ID_LANG_);
        $this->viewManager->initVariable(
            [
                'user' => $user,
            ]);


        echo $this->viewManager->render('pages/account.html', [
        ]);

    }

    public function edit()
    {
        $firstName = Tools::getValue('firstname');
        $lastName = Tools::getValue('lastname');
        $password = Tools::getValue('password');
        $password2 = Tools::getValue('password2');
        $user = new Users($this->user->id_user, _ID_LANG_);

        if (!$firstName || !$lastName || ($password && !$password2)) {
            $error = 'Required field empty';
        }
        if ($password2 != $password) {
            $error = 'password verification faild';
        }
        if ($password) {
            if (!Validate::isPasswd($password)) {
                $error = 'your password not success security ';

            }
        }


        if ($error) {
            $this->viewManager->initVariable(

                array('error' => Tools::translate($error),

                ));
            die($this->viewManager->render("pages/account.html"));
        } else {
            $user->firstname = $firstName;
            $user->lastname = $lastName;
            if ($password) {
                $user->newpassword = $password;
            }
            $user->update();
        }
        Tools::redirect(_BASE_URL_ . '/user/account');

    }

    public function login()
    {
        $email = Tools::getValue('email');
        $password = Tools::getValue('password');
        $id_user = Users::login($email, $password);
        if ($id_user) {
            $username = Users::getName($id_user);
            $username = array_shift($username);
            $username = $username['firstname'] . " " . $username['lastname'];
            $_SESSION['id_user'] = $id_user;
            $_SESSION['username_user'] = $username;
            Tools::redirect(_BASE_URL_ . '/user/account');
        } else {
            $this->viewManager->initVariable(

                array('error' => Tools::translate("Your login or your password not match"),

                ));
            echo $this->viewManager->render("pages/sigin.html");
        }
    }

    public function renewPassword()
    {
        $key = Tools::getValue("key");
        $context = Context::getContext();
        $is_form_step1 = Tools::isSubmit('sendemail');
        $is_form_step2 = Tools::isSubmit('sendemail2');
        if ($is_form_step1) {
            $email = Tools::getValue("email");
            $id_user = Users::emailExist($email);
            if ($id_user) {
                $user = new Users($id_user);
                $link = $user->genereateResetPasswordLink();
                Mail::send($user->email, 'Your password reset link', 'resetpassword', ['firstname' => $user->getFirstname(), 'lastname' => $user->getLastname(),
                    'link' => $context->getBaseurlLang() . 'user/lostpassword?link=' . $link
                ]);
                $this->viewManager->initVariable(

                    array('succes' => Tools::translate("reset link send by email"),

                    ));
            } else {
                $this->viewManager->initVariable(

                    array('error' => Tools::translate("Not account found"),

                    ));
            }

            echo $this->viewManager->render("pages/renewpassword.html");
        } else if ($is_form_step2) {
            $link = Tools::getValue("link");
            $id_user = Users::getUserIdFromLink($link);
            if ($id_user) {
                $error = '';
                $user = new Users($id_user);
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

                        array('error' => Tools::translate($error),

                        ));
                    die($this->viewManager->render("pages/renewpasswordform.html"));
                } else {

                    $user->newpassword = $password;
                    $user->update();
                }
                Tools::redirect(_BASE_URL_ . '/user/sigin');
            } else {
                $this->viewManager->initVariable(

                    array('error' => Tools::translate("link reset not valid"),

                    ));
                echo $this->viewManager->render("pages/renewpassword.html");
            }
        } else {
            $link = Tools::getValue("link");
            if ($link) {
                $id_user = Users::getUserIdFromLink($link);
                if (!$id_user) {
                    $this->viewManager->initVariable(

                        array('error' => Tools::translate("link reset not valid"),

                        ));
                    echo $this->viewManager->render("pages/renewpassword.html");
                } else {
                    $this->viewManager->initVariable(

                        array('link' => $link,

                        ));
                    echo $this->viewManager->render("pages/renewpasswordform.html");
                }

            } else {
                echo $this->viewManager->render("pages/renewpassword.html");
            }
        }

    }

    public function resetPassword()
    {
        $link = Tools::getValue("link");
        if ($link) {
            $id_user = Users::getUserIdFromLink($link);
            d($id_user);
        } else {
            echo $this->viewManager->render("pages/renewpassword.html");
        }
    }

    public function sigin()
    {

        echo $this->viewManager->render("pages/sigin.html");


    }

    public function logout()
    {
        $_SESSION['id_user'] = 0;

        unset($_SESSION['id_user']);
        Tools::redirect(_BASE_URL_);
    }

    public function addUser()
    {
        $error = '';
        $user = Users::createUserFromForm($error);
        if (!$user) {
            $this->viewManager->initVariable(

                array('error' => Tools::translate($error),

                ));
            die($this->viewManager->render("pages/register.html"));
        } else {
            $id_user = $user->save();
            $username = Users::getName($id_user);
            $username = array_shift($username);
            $username = $username['firstname'] . " " . $username['lastname'];
            $_SESSION['id_user'] = $id_user;
            $_SESSION['username_user'] = $username;
            Tools::redirect(_BASE_URL_ . '/user/account');
        }
    }
}
