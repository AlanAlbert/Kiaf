<?php
namespace kiaf;

class Autoload
{
    public static function autoload($class)
    {
    	
    }

    public static function registerAutoload()
	{
        spl_autoload_register(array('Autoload', 'autoload'), true, true);
    }
}
