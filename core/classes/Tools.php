<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 30/07/17
 * Time: 13:27
 */
class Tools
{
    public static function strpos($str, $find, $offset = 0, $encoding = 'UTF-8')
    {
        if (function_exists('mb_strpos')) {
            return mb_strpos($str, $find, $offset, $encoding);
        }
        return strpos($str, $find, $offset);
    }

    public static function strtolower($str)
    {
        if (is_array($str)) {
            return false;
        }
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($str, 'utf-8');
        }
        return strtolower($str);
    }

    static public  function substr ($str , $start , $length = false , $encoding = 'utf-8')
    {
        if ( is_array ($str) )
        {
            return false;
        }
        if ( function_exists ('mb_substr') )
        {
            return mb_substr ($str , intval ($start) , ($length === false ? Tools::strlen ($str) : intval ($length)) , $encoding);
        }
        return substr ($str , $start , $length);
    }

    static public function linkRewrite ($str , $utf8_decode = false)
    {
        $purified = '';
        $length = self::strlen ($str);
        if ( $utf8_decode )
        {
            $str = utf8_decode ($str);
        }
        for ($i = 0; $i < $length; $i++)
        {
            $char = self::substr ($str , $i , 1);
            if ( self::strlen (htmlentities ($char)) > 1 )
            {
                $entity = htmlentities ($char , ENT_COMPAT , 'UTF-8');
                $purified .= $entity[1];
            }
            elseif ( preg_match ('|[[:alpha:]]{1}|u' , $char) )
            {
                $purified .= $char;
            }
            elseif ( preg_match ('<[[:digit:]]|-{1}>' , $char) )
            {
                $purified .= $char;
            }
            elseif ( $char == ' ' )
            {
                $purified .= '-';
            }
        }
        return trim (self::strtolower ($purified));
    }
    static public function isSubmit ($submit)
    {
        return (isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y']) || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y']));
    }
    static public function redirectAdmin ($url)
    {

        $context = Context::getContext();
        header ('Location: ' . _BASE_URL_.'/'._ADMIN_URI_ . '/'. $context->getCurrentLanguage()->iso.  $url);

        exit;
    }
    static public function translate($string)
    {
        $context = Context::getContext();
        $translated = $context->getTranslator()->trans($string);
        return $translated;
    }
    static public function redirect ($url)
    {


            header ('Location: ' .  $url);

        exit;
    }

    public static function encrypt($password)
    {
        $options = [
            'cost' => 11,
        ];
        return password_hash( $password, PASSWORD_BCRYPT, $options);
    }



    public static function getValue($key, $default_value = false)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }
        $value = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));
        if (is_string($value)) {
            return stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($value))));
        }
        return $value;
    }

    public static function strlen($str, $encoding = 'UTF-8')
    {
        if (is_array($str)) {
            return false;
        }
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, $encoding);
        }
        return strlen($str);
    }


    public static function isEmpty($field)
    {
        return ($field === '' || $field === null);
    }

    static public function getConfig()
    {
        return Config::getInstance();
    }

    public static function displayError($string = 'Fatal error', $htmlentities = true, Context $context = null)
    {
        if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
            throw new BlogException($string);
        } else if ('Fatal error' !== $string) {
            return $string;
        }
    }


}
