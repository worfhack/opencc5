<?php

class ContactController extends FrontController
{
    
    public function index()
    {


            echo $this->viewManager->render("pages/contact.html");



    }
    public function sendMessage()
    {
        $gl_config = Config::getInstance();

        $name = Tools::getValue('name');
        $email = Tools::getValue("email");
        $phone = Tools::getValue("phone");
        $message = Tools::getValue("message");


        if (!$message || !$name || !$email || !$phone || !$message)
        {
            $this->viewManager->initVariable(

                array('error'=>Tools::translate('Missing field required'),

                ));


        }else {
            $this->viewManager->initVariable(

                array('message' => 'message envoyée',

                ));
            
            if ($gl_config['contactMail']){
                Mail::send($gl_config['contactMail'], Tools::translate("New Contact Email"), "contact",
                    [
                        'name'=>$name,
                        'email'=>$email,
                        'phone'=>$phone,
                        "message"=>$message
                    ]);
            }

        }
        echo $this->viewManager->render("pages/contact.html");
    }
}
