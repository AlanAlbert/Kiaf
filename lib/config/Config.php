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

class Config
{
    private static $config = array(
        'namespace_map' => [],
        'default_module' => 'home',
        'default_controller' => 'index',
        'default_action' => 'index',
        'left_delimiter' => '[{',
        'right_delimiter' => '}]',
        'default_jump_tpl' => 'default_jump.tpl',
    );

    /**
     * 解析配置文件
     * @method parseConfig
     * @return void
     */
    public static function parseConfig()
    {
        $config = include APP_CONFIG_PATH . 'config.php';
        self::$config = array_merge(self::$config, $config);
        if (USE_COMPOSER_AUTOLOAD && !empty(self::$config['namespace_map'])) {
            foreach (self::$config['namespace_map'] as $key => $value) {
                Autoload::setNamespaceMap($key, $value);
            }
        }
        // TODO
        // 解析XML、JSON
    }

    /**
     * 获取配置值
     * @method getValue
     * @param  string   $key 配置的键key
     * @return mixed        配置的值value
     */
    public static function getValue($key)
    {
        return self::$config[$key];
    }
}
