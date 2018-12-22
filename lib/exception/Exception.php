<?php
namespace kiaf\exception;

class Exception
{
    public static function registerExceptionHandler()
    {
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
    }

    public static function exceptionHandler($exception)
    {
        var_dump($exception);
    }
}
