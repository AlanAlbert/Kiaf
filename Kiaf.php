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

    /**
     * 运行框架
     * @method run
     * @param  string  $app_path              应用目录
     * @param  boolean $use_composer_autoload 是否使用composer autoload
     * @return void
     */
    public static function run($app_path = './Application', $use_composer_autoload = false)
    {
        # 初始化
        self::$app_path = $app_path;
        self::initFolder();
        self::initConst();
        define('USE_COMPOSER_AUTOLOAD', $use_composer_autoload);

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

    /**
     * 初始化常量
     * @method initConst
     * @return void
     */
    private static function initConst()
    {
        # 框架相关信息
        define('FRAMEWORK', 'Kiaf');
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
        define('APP_CACHE_PATH', APP_PATH . 'cache' . DS);
        define('APP_TPL_PATH', APP_PATH . 'tpl' . DS);

        # 应用根URL
        define('ROOT_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') .
            '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
    }

    /**
     * 初始化应用目录
     * @method initFolder
     * @return void
     */
    private static function initFolder()
    {
        if (!is_dir(self::$app_path)) {
            mkdir(self::$app_path, 0777, true);
        }
        $config_path = self::$app_path . DIRECTORY_SEPARATOR . 'config';
        $public_path = self::$app_path . DIRECTORY_SEPARATOR . 'public';
        $cache_path = self::$app_path . DIRECTORY_SEPARATOR . 'cache';
        $tpl_path = self::$app_path . DIRECTORY_SEPARATOR . 'tpl';
        $platform_path = self::$app_path . DIRECTORY_SEPARATOR . 'home';
        $controller_path = $platform_path . DIRECTORY_SEPARATOR . 'controller';
        $view_path = $platform_path . DIRECTORY_SEPARATOR . 'view';
        $model_path = $platform_path . DIRECTORY_SEPARATOR . 'model';

        # 创建应用文件夹
        $dirs = array(
            $platform_path,
            $controller_path,
            $view_path,
            $model_path,
            $config_path,
            $public_path,
            $cache_path,
            $tpl_path,
        );
        self::createFolders($dirs);

        # 创建应用配置文件
        $config_file = $config_path .
            DIRECTORY_SEPARATOR .
            'config.php';
        $config_content =
            '<?php' . PHP_EOL .
            '// 配置信息' . PHP_EOL .
            'return array(' . PHP_EOL . PHP_EOL .
            ');' . PHP_EOL;

        # 默认Index控制器
        $controller_index = $controller_path .
            DIRECTORY_SEPARATOR .
            'Index.php';
        $index_content =
            '<?php' . PHP_EOL .
            'namespace app\home\controller;' . PHP_EOL . PHP_EOL .
            'class Index' . PHP_EOL .
            '{' . PHP_EOL .
            '    public function index()' . PHP_EOL .
            '    {' . PHP_EOL .
            '        echo \'<h1>This is Kiaf!</h1>\';' . PHP_EOL .
            '    }' . PHP_EOL .
            '}' . PHP_EOL;

        # 默认跳转模板
        $default_jump_tpl = $tpl_path .
            DIRECTORY_SEPARATOR .
            'default_jump.tpl';
        $jump_tpl_content = <<<'HTML'
            <!DOCTYPE html>
            <html lang="en" dir="ltr">
                <head>
                    <meta charset="utf-8">
                    <title>跳转中...</title>
                </head>
                <body>
                    <h2><?php echo $msg; ?></h2>
                    <div id="time-tips">
                        <span id="timeout"><?php echo $timeout; ?></span>
                        秒后跳转至<a href="<?php echo $url; ?>">此处</a>...
                    </div>
                </body>
                <script type="text/javascript">
                    var timeout_tips = document.getElementById('timeout');
                    var timeout = parseInt(timeout_tips.innerHTML) * 1000;
                    var url = "<?php echo $url; ?>";
                    if (timeout >= 1000) {
                        setInterval(function () {
                            if (timeout > 0) {
                                timeout = timeout-1000 > 0 ? timeout-1000 : 0;
                                timeout_tips.innerHTML = timeout/1000;
                            }
                        }, 1000);
                    }
                </script>
            </html>
HTML;

        $files = array(
            $config_file => $config_content,
            $controller_index => $index_content,
            $default_jump_tpl => $jump_tpl_content,
        );
        self::createFiles($dirs);
    }

    /**
     * 创建文件夹
     * @method createFolders
     * @param  array        $dir_paths 文件夹路径数组
     * @return void
     */
    private static function createFolders($dir_paths)
    {
        foreach ($dir_paths as $value) {
            if (!is_dir($value)) {
                mkdir($value);
            }
        }
    }

    /**
     * 创建文件
     * @method createFile
     * @param  array     $files [文件路径 => 文件内容]
     * @return void
     */
    private static function createFiles($files)
    {
        foreach ($files as $key => $value) {
            if (!file_exists($key)) {
                file_put_contents($key, $value);
            }
        }
    }
}
