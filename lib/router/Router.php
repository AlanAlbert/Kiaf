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
    private static $router_map = array(

    );

    /**
     * 解析并执行请求
     * @method parseRequest
     * @return void
     */
    public static function parseRequest()
    {
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        $query_string = self::$router_map[$query_string] ?? $query_string;
        parse_str($query_string, $params);
        define('CURRENT_MODULE', strtolower($params['m'] ?? Config::getValue('default_module')));
        define('CURRENT_CONTROLLER', strtolower($params['c'] ?? Config::getValue('default_controller')));
        define('CURRENT_ACTION', strtolower($params['a'] ?? Config::getValue('default_action')));
        unset($params['m']);
        unset($params['c']);
        unset($params['a']);

        $post_data = $_POST ?? array();

        // 加载不同平台的控制器
        $controller_path = '\\' . Config::getValue('app_root_namespace') . CURRENT_MODULE .
            '\\controller' .
            '\\' . ucfirst(CURRENT_CONTROLLER);
        $controller = new $controller_path();
        $action = CURRENT_ACTION;
        $controller->$action($params, $post_data);
    }

    /**
     * 映射特殊路由到普通路由
     * 如：dispatch('test/test', 'm=test/c=test')
     * @method dispatch
     * @param  string   $src 匹配路由
     * @param  string   $des 目标路由
     * @return void
     */
    public static function dispatch(string $src, string $des)
    {
        self::$router_map[$src] = $des;
    }
}
