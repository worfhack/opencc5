<?php

class HomeController extends FrontController
{

    public function index()
    {

        $collectionManager = new ArticleCollection($this->id_lang);
        $collectionManager->getLastPublication();
        $collectionManager->load();
        $this->viewManager->initVariable(

            array('listLastPub' => $collectionManager)
        );

        echo $this->viewManager->render("pages/home.html");


    }

}
