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


    static public function getModelValue($node, $element, $lang_field = false)
    {

        if ($lang_field == true) {
            $array_lang_field = array();
            foreach ($node->$element->language as $l) {
                $id_lang = (string)$l->attributes();
                $value = (string)$l;

                $array_lang_field[$id_lang] = $value;

            }
            return $array_lang_field;
        } else {
            return (string)$node->$element;
        }
    }


    static public function getModelApi($webService, $route, $id, $root)
    {

        $opt = array();
        $opt['resource'] = $route . '/' . $id;
        $xml = $webService->get($opt);
        $model = $xml->$root->children();

        return $model;


    }
    static public function isSubmit ($submit)
    {
        return (isset($_POST[$submit]) OR isset($_POST[$submit . '_x']) OR isset($_POST[$submit . '_y']) OR isset($_GET[$submit]) OR isset($_GET[$submit . '_x']) OR isset($_GET[$submit . '_y']));
    }
    static public function redirect ($url)
    {


            header ('Location: ' .  $url);

        exit;
    }
    static public function encrypt ($passwd)
    {

        return md5 (_KEY_ . $passwd);
    }


    static public function translate($string, ...$var)
    {
        global $gl_trad;
        $md5 = md5($string);
        if (isset($gl_trad) && isset($gl_trad[$md5]))
        {
            $source = $gl_trad[$md5];
        }else
        {
            $source = $string;
        }

        return vsprintf($source , $var);

    }

    static public function fetchModelApi($root, $need_vars, $mapping = [], $lang_fields = [])
    {
        $return = new ApiResult();


        foreach ($need_vars as $v) {
            if (in_array($v, $lang_fields)) {
                $lang_field = true;
            } else {
                $lang_field = false;
            }
            $data = Tools::getModelValue($root, $v, $lang_field);


            if (array_key_exists($v, $mapping)) {



                $name_key = $mapping[$v];
                if (is_callable($mapping[$v]))
                {
                    $mapping[$v]($data, $name_key);
                }
            } else {
                $name_key = $v;
            }
            $return->$name_key = $data;
        }
        return $return;

    }
static function xml_attribute($object, $attribute, $default='')
    {
        $atts_array = (array) $object;
        $atts_array = $atts_array['@attributes'];
        if(isset($atts_array[$attribute]))
            return (string) $atts_array[$attribute];
        return $default;

    }
    static public function getCollections($webService, $route, $filter, $root,  $sort='')
    {

        $opt = array();

        $query_data = array('filter' => $filter);
        $params = urldecode(http_build_query($query_data).($sort?'&sort='.$sort: ''));
        $opt['resource'] = $route . '/?' . $params;
        $xml = $webService->get($opt);
        $collection = $xml->$root->children();
        return $collection;


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

    static function need_to_be_cgi()
    {
        global $gl_context;
        if ($gl_context != 'cgi') {
            die(self::displayError('Need to be lunch in cgi mode'));
        }
    }

    public static function isEmpty($field)
    {
        return ($field === '' || $field === null);
    }

    static public function get_config()
    {
        global $gl_config;
        return $gl_config;
    }


    public static function displayError($string = 'Fatal error', $htmlentities = true, Context $context = null)
    {
        if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
            throw new FastPastToursException($string);
        } else if ('Fatal error' !== $string) {
            return $string;
        }
        //return Context::getContext()->getTranslator()->trans('Fatal error', array(), 'Admin.Notifications.Error');
    }


}