<?php
/**
 * Config.php 配置文件解析类 
 * @author: Alan_Albert <alanalbert@qq.com> 
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-19 13:47:51 
 * @last modified by: Alan_Albert <alanalbert@qq.com> 
 */
namespace kiaf\config;

class Config
{
    private static $config = array(
        // 'namespace_map' => [],
        'default_module' => 'home',
        'default_controller' => 'index',
        'default_action' => 'index',
    );

    public static function parseConfig()
    {
        $config = include APP_CONFIG_PATH . 'config.php';
        self::$config = array_merge(self::$config, $config);
        // TODO
        // 解析XML、JSON
    }

    public static function getValue(string $key)
    {
        return self::$config[$key];
    }
}
