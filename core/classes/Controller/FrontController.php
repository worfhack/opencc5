<?php


class FrontController extends Controller
{



    function __construct()
    {
        $this->viewManager = new RenderFront(['page_name'=>$this->pageName]);

        parent::__construct();
    }
}