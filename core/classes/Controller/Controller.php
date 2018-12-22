<?php


abstract class Controller
{

    protected $viewManager;
    protected $context;
    protected $id_lang;
    protected $pageName = '';
    protected $need_to_be_log = false;


    public function __construct()
    {

        $this->context = Context::getContext();
        $this->id_lang = $this->context->getCurrentLanguage()->id_lang;

        $this->viewManager->initVariable(

            array('page_name'=>$this->pageName,

            ));
        //
        define('_BASE_URL_LANG_',$this->context->getBaseurlLang());
        define('_BASE_ADMIN_URL_LANG_', _BASE_URL_.'/'._ADMIN_URI_ . '/'.$this->context->getCurrentLanguage()->iso .'/');
        define('_BASE_ADMIN_URL_', _BASE_URL_.'/'._ADMIN_URI_ );
    }
}