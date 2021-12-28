<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 19/10/2018
 * Time: 15:09
 */
class Language extends ObjectModel
{
    public $identifier = 'id_lang';
    public $table = 'lang';


    public $name;
    public $title;
    public $iso;
    public static $_language;
    public $local;
    public $id_lang;
    static public function getLanguages($only_actif = false)
    {
        if (self::$_language == NULL) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'lang`';
            self::$_language = Db::getInstance()->executeS($sql);
        }
        return self::$_language;
    }
   static  public function loadLanguageByIso($iso)
    {
        $sql = 'SELECT l.* from ' . _DB_PREFIX_ . 'lang l where iso = "' . $iso.'"';
        $lang =  Db::getInstance()->getRow($sql);
       if (!$lang)
           return false;
        return self::toObject($lang);
    }
}
