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
    protected $translator;
    public function render($template, $display=true)
    {

        $text =  $this->twig->render($template, $this->params);
        if ($display)
        {
            echo $text;
        }
        else
        {
            return $text;
        }
    }

    public function initVariable($params)
    {
        if ($params) {
            $this->params = array_merge($this->params, $params);
        }
    }
    public function __construct()
    {
        $context = Context::getContext();
        $this->params['baseUrl'] = $context->getBaseurl();
        $this->params['baseUrlLang'] = $context->getBaseurlLang();
        $this->params['currentPage'] = $context->getCurrentUrl();
        $this->params['siteName'] = $context->getConfig('_SITE_NAME_');
        $this->params['siteBaseLine'] = $context->getConfig('_SITE_BASE_LINE_');
        $this->params['siteTitle'] = $context->getConfig('_SITE_TITLE_');
        $this->params['cvPath'] = $context->getConfig('_CV_PATH_');
        $this->loader = new Twig_Loader_Filesystem(VIEW_DIR . $this->baseTpl);

        $this->twig_functions[] = new Twig_SimpleFunction('displayDate', function ($dateTime, $format) {
                $context = Context::getContext();
            $moment = new \Moment\Moment($dateTime, _LOCAL_ZONE_);
            \Moment\Moment::setLocale($context->getCurrentLanguage()->local);
            return ($moment->format($format, new \Moment\CustomFormats\MomentJs())); // 2016-09-13T14:32:06+0100
        });
        $this->twig_functions[] = new Twig_SimpleFunction('cleanUrl', function ($string) {
            $pattern = '!([^:])(//)!';

            $url = _BASE_URL_.'/' . $string;
            return preg_replace($pattern,  "$1/", $url);
        });
        $this->twig_functions[] = new Twig_SimpleFunction('add_js_var', function ($string) {

            return $string;
        });
        $this->twig_functions[] = new Twig_SimpleFunction('base_admin_url', function () {
            $url = _BASE_ADMIN_URL_;
            return $url ;
        });
        $this->twig_functions[]= new Twig_Function('trans', function ($string) {
         return Tools::translate($string);
        });
        $this->twig_functions[] = new Twig_SimpleFunction('base_admin_url_lang', function () {
            $url = _BASE_ADMIN_URL_LANG_;
            return $url ;
        });

        $this->twig_functions[] = new Twig_SimpleFunction('base_url_lang', function () {
            $url = _BASE_URL_LANG_;
            return $url ;
        });

        $this->twig_functions[] = new Twig_SimpleFunction('base_url', function () {
            $url = _BASE_URL_;
            return $url ;
        });


        $this->twig = new Twig_Environment(  $this->loader, array(

            'debug' => true,
        ));

        $this->twig->addExtension(new Twig_Extension_Debug());
        foreach ($this->twig_filters as $filter)
            $this->twig->addFilter($filter);
        foreach ($this->twig_functions as $func)
            $this->twig->addFunction($func);


    }
}
