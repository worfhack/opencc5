<?php

class CoreException extends Exception
{
    protected $messageArray = [];

    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function displayMessage()
    {

    }

}
