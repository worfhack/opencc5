<?php


class FrontController extends Controller
{



    function __construct()
    {

         $menuCat = Category::getTree();
        $this->viewManager = new RenderFront(['page_name'=>$this->pageName]);
        parent::__construct();
        $this->viewManager->initVariable(

            array('menuCat'=>$menuCat,

            ));

    }
}