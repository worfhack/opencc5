<?php

class ContactController extends FrontController
{
    
    public function index()
    {


            echo $this->viewManager->render("pages/contact.html");



    }
    public function sendMessage()
    {
        $this->viewManager->initVariable(

            array('message'=>'message envoyée',

            ));
        echo $this->viewManager->render("pages/contact.html");
    }
}
