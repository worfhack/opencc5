<?php


class AdminController extends Controller
{

    protected  $employee;
    protected $need_to_be_log = true;
    public function __construct()
    {
        $this->employee =  Employee::getEmployee();


        if ($this->employee == false && $this->need_to_be_log == true)
        {
            Tools::redirect(_ADMIN_URL_.'/login');
        }else {

            $loader = new Twig_Loader_Filesystem(VIEW_DIR . 'admin/');




            if ($this->employee)
            {


                $roles = Employee::getMyRoles();
                $allRules =  Role::getAllSystemRules();
                foreach ($allRules  as $rules)
                {
                    $name   = str_replace('-', '_', $rules);

                    if ($roles && in_array($rules, $roles))
                    {
                        $val = true;
                    }else
                    {
                        $val = false;
                    }
                    $this->js_vars[$name]  = (int)$val;
                    $this->global_var[$name]  = $val;
                }

                $this->global_var['mail']  = $this->employee->mail;
                $this->global_var['firstname']  = $this->employee->firstname;
                $this->global_var['lastname']  = $this->employee->lastname;
                $this->global_var['picture_full']  = $this->employee->picture_full;
                $this->global_var['picture_min']  = $this->employee->picture_min;


            }

            $date = date_create();
            $this->global_var['today_time']  =$date->format('Y/m/d');

            parent::__construct($loader);

        }
        // Tools::redirect();

    }
}