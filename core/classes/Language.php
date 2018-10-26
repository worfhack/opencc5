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

    public $id_lang;

   static  public function loadLanguageByIso($iso)
    {
        $sql = 'SELECT l.* from ' . _DB_PREFIX_ . 'lang l where iso = "' . $iso.'"';
        $lang =  Db::getInstance()->getRow($sql);
       if (!$lang)
           return false;
        return self::toObject($lang);
    }
}