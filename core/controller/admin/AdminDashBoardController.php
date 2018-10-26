<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminDashBoardController extends AdminController
{


    public function index()
    {
        echo $this->viewManager->render("pages/dash.html");
    }
}