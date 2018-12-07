<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 20/10/2018
 * Time: 19:10
 */
class Context
{
    static $_context;
    private $requestUri;
    private $currentLanguage;
    private $gl_config;
    private $currentPage;
    private $baseUrl;
    private $baseUrlLang;
    private $server_request_scheme;
    private $config;
    private function __construct()
    {
        global $gl_config;

        $this->gl_config = $gl_config;
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->setLanguage()->setCurrentUrl()->setBaseUrl()->setBaseUrlLang()->setConfig();
    }
    public function setConfig()
    {
        $this->config = Configuration::getAllConfig();
        return $this;
    }
    public function getConfig($key=false, $default=0)
    {
      if ($key)
      {
          if (isset( $this->config[$key])) {
              return $this->config[$key];
          }return $default;
      }
      return $this->config;
    }
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }
    public function getBaseurl()
    {

        return $this->baseUrl;
    }
    public function getBaseurlLang()
    {

        return $this->baseUrlLang;
    }
    private function setBaseUrl()
    {

        if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
            (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
            $this->server_request_scheme = 'https';
        } else {
            $this->server_request_scheme = 'http';
        }

        $this->baseUrl = $this->server_request_scheme.'://'.$_SERVER['HTTP_HOST'] .'/';
        return $this;
    }
    private function setBaseUrlLang()
    {
        $this->baseUrlLang = $this->server_request_scheme.'://'.$_SERVER['HTTP_HOST'] .'/' . $this->currentLanguage->iso . '/';
        return $this;
    }
    public function getCurrentUrl()
    {
        $this->currentPage =$this->requestUri ;
        $this->currentPage = str_replace('//', '/', $this->requestUri);
        return $this->currentPage;
    }
    private function setCurrentUrl()
    {
        $this->currentPage =$this->requestUri ;
        $this->currentPage = str_replace('//', '/', $this->requestUri);
        return $this;
    }

    private function setLanguage()
    {

        $result = [];
        if (preg_match('~^/[a-z]{2}(?:/|$)~', $this->requestUri, $result)) {
            $iso = $result[0];

            $this->requestUri = '/' . str_replace($iso, '', $this->requestUri);
            $iso = str_replace('/', '', $iso);
            $this->currentLanguage = Language::loadLanguageByIso($iso);
            if (!$this->currentLanguage) {
                $this->currentLanguage = new Language($this->gl_config['id_lang']);
            }

        }

       else  if (preg_match('~^/'._ADMIN_URI_.'/([a-z]{2})(?:/|$)~', $this->requestUri, $result)) {

            $iso = $result[1];

          $this->requestUri = str_replace($iso, '', $this->requestUri);
            $this->requestUri = str_replace("//", '/', $this->requestUri);
           $iso = str_replace('/', '', $iso);

            $this->currentLanguage = Language::loadLanguageByIso($iso);
            if (!$this->currentLanguage) {
                $this->currentLanguage = new Language($this->gl_config['id_lang']);
            }
        } else {
            if (isset($_SESSION['current_id_lang'])) {
                $this->currentLanguage = new Language($this->gl_config['id_lang']);
            }
            if (!$this->currentLanguage) {
                $this->currentLanguage = new Language($this->gl_config['id_lang']);
            }
        }
        $_SESSION['current_id_lang'] = $this->currentLanguage->id_lang;
        define('_ID_LANG_', $this->currentLanguage->id_lang);
        return $this;

    }

    static public function getContext()
    {
        if (!isset(self::$_context)) {
            self::$_context = new Context();
        }
        return self::$_context;
    }

}