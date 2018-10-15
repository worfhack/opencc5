<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 30/07/17
 * Time: 16:40
 */
class Configuration extends ObjectModel
{

    public $identifier = 'id_configuration';
    public $table = 'configuration';


    public $id_configuration;
    public $value_lang;
    public $description;
    public $name;
    public $value;
    public $fields_lang = array('value_lang');

    static public function getAllConfig()
    {
        $sql = 'SELECT c.*, cl.* from ' . _DB_PREFIX_ . 'configuration c JOIN ' . _DB_PREFIX_ . 'configuration_lang cl
    ON c.id_configuration = cl.id_configuration and cl.id_lang = ' . _ID_LANG_ . '
    ';
        return Db::getInstance()->ExecuteS($sql, $array = true, $memcached = false);

    }

    function getValue()
    {
        if (is_array($this->value_lang)) {
            $value_lang = $this->value_lang[_ID_LANG_];
        } else {
            $value_lang = $this->value_lang;
        }
        if (!$value_lang) {
            return $this->value;
        }
    }

    static public function getConfigByName($name)
    {
        return ObjectModel::getSingleInfo('configuration', 'name', $name, 'id_configuration', false);
    }

    static public function getCleanConfig($configEdited = false)
    {
        if (empty($_POST['name']) || empty($_POST['description'])) {
            return false;
        }


        $config = new Configuration();
        $config->name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $config->value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);
        $config->value_lang = filter_input(INPUT_POST, 'value_lang', FILTER_SANITIZE_STRING);
        // $employee->phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $config->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);


        return $config;
    }
}