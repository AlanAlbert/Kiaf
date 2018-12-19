<?php
/**
 * Kiaf.php
 * 框架启动文件
 * @author: Alan <alanalbert@qq.com>
 * @version: 0.0.1
 * @copyright: Alan
 */
namespace kiaf;

class Kiaf
{
    private static $app_path;
    // private static $platform = 'Home';
    // private static $controller;
    // private static $action;

    public static function run(string $app_path = './Application')
    {
        self::$app_path = $app_path;
        self::initFolder();
        self::initConst();
    }

    private static function initConst()
    {
        # 框架相关信息
        define('FRAMWORK', 'kiaf');
        define('VERSION', '0.0.1');

        # 目录分隔符
        define('DS', DIRECTORY_SEPARATOR);

        # 框架核心路径
        define('FRAMWORK_PATH', __DIR__ . DS);
        define('LIBRARY_PATH', FRAMWORK_PATH . 'lib' . DS);
        define('AUTOLOAD_PATH', LIBRARY_PATH . 'autoload' . DS);
        define('CONFIG_PATH', LIBRARY_PATH . 'config' . DS);
        define('ERROR_PATH', LIBRARY_PATH . 'error' . DS);
        define('EXCEPTION_PATH', LIBRARY_PATH . 'exception' . DS);
        define('ROUTER_PATH', LIBRARY_PATH . 'router' . DS);
        define('DATABASE_PATH', LIBRARY_PATH . 'database' . DS);
        
        # 应用路径
        define('APP_PATH', realpath(self::$app_path) . DS);
        define('APP_CONFIG_PATH', APP_PATH . 'config' . DS);
        define('APP_PUBLIC_PATH', APP_PATH . 'public' . DS);
    }

    private static function initFolder()
    {
        if (!is_dir(self::$app_path)) {
            mkdir(self::$app_path, 0777, true);
        }
        $config_path = self::$app_path . DIRECTORY_SEPARATOR . 'config';
        $public_path = self::$app_path . DIRECTORY_SEPARATOR . 'public';
        $platform_path = self::$app_path . DIRECTORY_SEPARATOR . 'Home';
        $controller_path = $platform_path . DIRECTORY_SEPARATOR . 'Controller';
        $view_path = $platform_path . DIRECTORY_SEPARATOR . 'View';
        $model_path = $platform_path . DIRECTORY_SEPARATOR . 'Model';
        if (!is_dir($platform_path)) {
            mkdir($platform_path);
        }
        if (!is_dir($controller_path)) {
            mkdir($controller_path);
        }
        if (!is_dir($view_path)) {
            mkdir($view_path);
        }
        if (!is_dir($model_path)) {
            mkdir($model_path);
        }
        if (!is_dir($config_path)) {
            mkdir($config_path);
        }
        if (!is_dir($public_path)) {
            mkdir($public_path);
        }
    }
}
