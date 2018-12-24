<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 30/07/17
 * Time: 13:19
 */
class DbException extends BlogException
{
    public function __toString()
    {
        return $this->message;
    }
}
