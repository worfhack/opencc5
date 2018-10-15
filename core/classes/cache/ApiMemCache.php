<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 17/08/17
 * Time: 11:43
 */
class ApiMemCache
{
    private static $_memcached;
    private $Memcached;
    private function __construct()
    {
        if(! class_exists('Memcached')) {
            eval('
      class Memcached
      {
              function get()
              {
                  return NULL;
              }
              
              function set()
              {
              
                  return NULL;
              }

              function flush(){
              }
              
              function addServer()
              {
              
              }
      
      }
      
      ');

        }
        $this->Memcached = new Memcached();
        $this->Memcached->addServer(_MEMCACHED_SERVER_, _MEMCACHED_PORT_);
    }

    public static function getInstance()
    {
        if (!self::$_memcached)
        {
            self::$_memcached = new ApiMemCache();
        }
        return self::$_memcached->Memcached;
    }
}

