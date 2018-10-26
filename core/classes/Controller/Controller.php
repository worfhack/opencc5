<?php


abstract class Controller
{

    protected $viewManager;
    protected $context;
    protected $id_lang;
    protected $pageName = '';


    public function __construct()
    {

        $this->context = Context::getContext();
        $this->id_lang = $this->context->getCurrentLanguage()->id_lang;
    }
}