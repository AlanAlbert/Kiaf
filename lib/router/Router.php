<?php
namespace kiaf;

class Router
{
    private static $platform;
    private static $controller;
    private static $action;
    private static $router_map = array();

    public static function parserRequest()
    {
        self::$platform = $_REQUEST['p'] ?? Config::getValue('default_platform');
        self::$controller = $_REQUEST['c'] ?? Config::getValue('default_controller');
        self::$action = $_REQUEST['a'] ?? Config::getValue('default_action');
        // Config::getValue() TODO
        // 加载不同平台的控制器
        $controller = new self::$controller();
        $controller->self::$action();
    }

    public static function dispatch(string $src, string $des)
    {
        self::$router_map[$src] = $des;
    }
}
