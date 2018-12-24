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

use kiaf\autoload\Autoload;
use kiaf\database\Db;

class Config
{
    private static $config = array(
        # 命名空间-路径 映射关系
        'namespace_map' => [

        ],

        # 应用下根命名空间
        'app_root_namespace' => 'app\\',

        # 默认模块、控制器、方法
        'default_module' => 'home',
        'default_controller' => 'index',
        'default_action' => 'index',

        # 视图左右定界符
        'left_delimiter' => '[{',
        'right_delimiter' => '}]',

        # 跳转模板
        'jump_tpl' => 'default_jump.tpl',
        // 'error_handler_tpl' => 'default_error_handler.tpl',

        # 数据库配置
        'database' => array(
            // 'db_type' => '',
            // 'db_host' => '',
            // 'db_port' => '',
            // 'db_user' => '',
            // 'db_pwd' => '',
            // 'db_name' => '',
            // 'db_char_set' => '',
        ),
    );

    /**
     * 解析配置文件，并将相关配置传到
     * @method parseConfig
     * @return void
     */
    public static function loadConfig() : void
    {
        $config = include APP_CONFIG_PATH . 'config.php' ?? [];
        self::$config = array_merge(self::$config, $config);

        # 命名空间加载到Autoload
        if (!USE_COMPOSER_AUTOLOAD) {
            if (!empty(self::$config['namespace_map'])) {
                foreach (self::$config['namespace_map'] as $key => $value) {
                    Autoload::setNamespaceMap($key, $value);
                }
            }
            if (!empty(self::$config['app_root_namespace'])) {
                Autoload::setNamespaceMap(self::$config['app_root_namespace'],
                    APP_PATH);
            }
        }

        # 数据库配置加载到Db
        if (!empty(self::$config['database'])) {
            Db::setConfig(self::$config['database']);
        }
    }

    /**
     * 获取配置值
     * @method getValue
     * @param  string   $key 配置的键key
     * @return mixed        配置的值value
     */
    public static function getValue(string $key)
    {
        return self::$config[$key];
    }
}
