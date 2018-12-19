<?php
/**
 * Autoload.php 自动加载类 
 * @author: Alan_Albert <alanalbert@qq.com> 
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-19 13:22:36 
 * @last modified by: Alan_Albert <alanalbert@qq.com>
 */
namespace kiaf\autoload;

use kiaf\config\Config;

class Autoload
{
    private static $namespace_map = array(
        '\\app' => APP_PATH,
        '\\kiaf' => LIBRARY_PATH,
    );

    private static function autoload($class_name)
    {
        $class_info = self::parseClassName($class_name);
        $dir = '';
        $namespace = '';
        foreach ($class_info['namespace_part'] as $value) {
            $dir .= $value . DS;
            $namespace .= '\\' . $value;
            $dir = self::$namespace_map[$namespace] ?? $dir;
        }
        $class_path = $dir . $class_info['class'] . '.php';
        if (is_file($class_path)) {
            require $class_path;
        } else {
            throw new \Error($class_path . ' not found');
        }
    }

    private static function parseClassName(string $class_name)
    {
        $body = explode('\\', $class_name);
        $result['class'] = array_pop($body);
        $result['namespace'] = implode('\\', $body);
        $result['namespace_part'] = $body;
        return $result;
    }

    public static function registerAutoload()
	{
        spl_autoload_register(array('\kiaf\autoload\Autoload', 'autoload'), true, true);
    }

    public static function setNamespaceMap(string $namespace, string $dir)
    {
        if (isset(self::$namespace_map[$namespace])) {
            return false;
        }
        self::$namespace_map[$namespace] = $dir;
        return true;
    }

    public static function getNamespaceMap(string $namespace)
    {
        return self::$namespace_map[$namespace] ?? null;
    }
}
