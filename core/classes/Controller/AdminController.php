<?php


class AdminController extends Controller
{

    protected  $admin;
    protected $need_to_be_log = true;
    public function __construct()
    {
        $this->admin =  Administrator::getAdministrator();
        $this->viewManager = new RenderAdmin(['page_name' => $this->pageName]);

        $this->viewManager->initVariable(

            array('base_admin_url'=>_BASE_URL_.'/'._ADMIN_URI_,'page_name'=>$this->pageName,
                'base_admin'=>_BASE_URL_.'/',

            ));
        //
        if ($this->admin == false && $this->need_to_be_log == true) {
            Tools::redirect(_BASE_URL_.'/'._ADMIN_URI_ . '/login');
        }

        parent::__construct();


    }
}