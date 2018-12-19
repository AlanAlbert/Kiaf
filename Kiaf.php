<?php
/**
 * Kiaf.php 框架入口类 
 * @author: Alan_Albert <alanalbert@qq.com> 
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-19 13:21:40 
 * @last modified by: Alan_Albert <alanalbert@qq.com> 
 */
namespace kiaf;

use kiaf\config\Config;
use kiaf\router\Router;

class Kiaf
{
    private static $app_path;

    public static function run(string $app_path = './Application', bool $use_composer_autoload = false)
    {
        # 初始化
        self::$app_path = $app_path;
        self::initFolder();
        self::initConst();

        # 自动加载
        if (!$use_composer_autoload) {
            require AUTOLOAD_PATH . 'autoload.php';
            \kiaf\autoload\Autoload::registerAutoload();
        }

        # 载入配置文件
        Config::parseConfig();

        # 路由
        Router::parseRequest();
    }

    private static function initConst()
    {
        # 框架相关信息
        define('FRAMWORK', 'kiaf');
        define('VERSION', '0.0.1');

        # 目录分隔符
        define('DS', DIRECTORY_SEPARATOR);

        # 框架核心路径
        define('FRAMEWORK_PATH', __DIR__ . DS);
        define('LIBRARY_PATH', FRAMEWORK_PATH . 'lib' . DS);
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
        $platform_path = self::$app_path . DIRECTORY_SEPARATOR . 'home';
        $controller_path = $platform_path . DIRECTORY_SEPARATOR . 'controller';
        $view_path = $platform_path . DIRECTORY_SEPARATOR . 'view';
        $model_path = $platform_path . DIRECTORY_SEPARATOR . 'model';

        # 创建应用文件夹
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
        
        # 创建应用配置文件
        if (!file_exists($config_path . DIRECTORY_SEPARATOR . 'config.php')) {
            $config_content = '<?php' . PHP_EOL .
                '// 配置信息' . PHP_EOL . 
                'return array(' . PHP_EOL . PHP_EOL . 
                ');' . PHP_EOL;
            file_put_contents($config_path . DIRECTORY_SEPARATOR . 'config.php',
                $config_content);
        }

        if (!file_exists($controller_path . DIRECTORY_SEPARATOR . 'Index.php')) {
            $index_content = '<?php' . PHP_EOL . 
                'namespace app\home\controller;' . PHP_EOL . PHP_EOL . 
                'class Index' . PHP_EOL . 
                '{' . PHP_EOL . 
                '    public function index()' . PHP_EOL . 
                '    {' . PHP_EOL . 
                '        echo \'<h1>This is Kiaf!</h1>\';' . PHP_EOL . 
                '    }' . PHP_EOL . 
                '}' . PHP_EOL;
            file_put_contents($controller_path . DIRECTORY_SEPARATOR . 'Index.php', 
                $index_content);
        }
    }
}
