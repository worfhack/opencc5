<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 15/10/2018
 * Time: 16:47
 */
class Render
{
    protected $baseTpl;
    protected $twig_filters = [];
    protected $twig_functions = [];
    protected $loader;
    protected $js_vars = [];
    protected $twig;
    protected $global_var = [];
    protected $params= [];

    public function render($template)
    {
        echo $this->twig->render($template, $this->params);
    }
    public function initVariable($params)
    {
        $this->params = $params;
    }
    public function __construct()
    {



        $this->loader = new Twig_Loader_Filesystem(VIEW_DIR . $this->baseTpl);
        $this->twig_filters[] = new Twig_Filter('trans', function ($string, ...$var) {
            return Tools::translate($string, $var);
        });
        $this->twig_filters[] = new Twig_Filter('datetime', function ($d, $format) {


            if ($d instanceof \DateTime) {
                $d = $d->getTimestamp();
            }else
            {
                $d = strtotime($d);

            }

            return strftime($format, $d);
        });

        $this->twig_functions[] = new Twig_SimpleFunction('add_js_var', function ($string) {

            return $string;
        });


        $this->twig_functions[] = new Twig_SimpleFunction('base_url_lang', function () {
            $url = _BASE_URL_LANG_;
            return $url ;
        });






        $this->twig = new Twig_Environment(  $this->loader, array(

            'debug' => true,
        ));

//        $this->twig->addExtension(new Twig_Extension_Debug());
//        foreach ($this->twig_filters as $filter)
//            $this->twig->addFilter($filter);
//        foreach ($this->twig_functions as $func)
//            $this->twig->addFunction($func);
//
//
//        foreach ($this->global_var  as $itemName=>$itemVal)
//        {
//
//            $this->twig->addGlobal($itemName, $itemVal);
//
//        }
//        $js_var_text  = '';
//        foreach ($this->js_vars  as $itemName=>$itemVal)
//        {
//            $js_var_text .= 'var ' . $itemName . ' = ';
//            switch (gettype($itemVal))
//            {
//                case "boolean":
//                default:
//                    $js_var_text .= $itemVal;
//
//
//            }
//
//            $js_var_text .= ';';
//        }
//
//        $this->twig->addGlobal('global_js', $js_var_text);
//        $this->twig->addGlobal('page_name', $this->pageName);
//        $this->twig->addGlobal('base_url', _BASE_URL_);
//        $this->twig->addGlobal('base_url_front', _BASE_URL_);
//        $this->twig->addGlobal('current_page', _CURRENT_URL_);


    }
}