<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 30/07/17
 * Time: 16:40
 */
class Configuration extends ObjectModel
{

    public $identifier = 'id_config';
    public $table = 'config';


    public $id_config;
    public $value_lang;
    public $description;
    public $key;
    public $value;
    public $fields_lang = array('value_lang');

    static public function getAllConfig()
    {
        $config = [];
        $sql = 'SELECT c.key,c.value,  cl.value_lang from ' . _DB_PREFIX_ . 'config c JOIN ' . _DB_PREFIX_ . 'config_lang cl
    ON c.id_config = cl.id_config and cl.id_lang = ' . _ID_LANG_ . '
    ';
        $result =  Db::getInstance()->executeS($sql, true);
        foreach ($result as $r)
        {
            $config[$r['key']] = ($r['value_lang']!= ''?$r['value_lang']:$r['value']);
        }

        return $config;
    }

public function getValue()
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
        $config->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);


        return $config;
    }
}
