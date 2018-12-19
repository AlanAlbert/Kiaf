<?php
/**
 * Router.php 路由类
 * @author: Alan_Albert <alanalbert@qq.com> 
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-19 14:09:50 
 * @last modified by: Alan_Albert <alanalbert@qq.com> 
 */
namespace kiaf\router;

use kiaf\config\Config;

class Router
{
    private static $module;
    private static $controller;
    private static $action;
    private static $router_map = array();

    public static function parseRequest()
    {
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        $query_string = self::$router_map[$query_string] ?? $query_string;
        $params = self::parseQueryString($query_string);
        self::$module = strtolower($params['m'] ?? Config::getValue('default_module'));
        self::$controller = strtolower($params['c'] ?? Config::getValue('default_controller'));
        self::$action = strtolower($params['a'] ?? Config::getValue('default_action'));

        // 加载不同平台的控制器
        $controller_path = '\\app\\' . self::$module . 
            '\\controller' . 
            '\\' . ucfirst(self::$controller);
        $controller = new $controller_path();
        $action = self::$action;
        $controller->$action();
    }

    public static function dispatch(string $src, string $des)
    {
        self::$router_map[$src] = $des;
    }

    private static function parseQueryString(string $query_string) : array
    {
        $params = array();
        if (!$query_string) {
            return $params;
        }
        $query_part = explode('&', $query_string);
        foreach ($query_part as $value) {
            list($params['key'], $params['value']) = explode('=', $value);
        }
        return $params;
    }
}
