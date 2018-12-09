<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 30/07/17
 * Time: 16:40
 */
class Media extends ObjectModel
{

    public $identifier = 'id_media';
    public $table = 'media';


    public $id_media;
    public $name;

    public $json_fields = ['id_media', 'name'];

}