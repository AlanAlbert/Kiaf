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

class Autoload
{
    private static $namespace_map = array(
        'kiaf\\' => LIBRARY_PATH,
    );

    /**
     * 自动加载回调函数
     * @param string $class_name 类名
     * @return void
     */
    private static function autoload(string $class_name) : void
    {
        $class_info = self::parseClassName($class_name);
        $dir = '';
        $namespace = '';
        foreach ($class_info['namespace_part'] as $value) {
            $dir .= $value . DS;
            $namespace .= $value . '\\';
            $dir = self::$namespace_map[$namespace] ?? $dir;
        }
        $class_path = $dir . $class_info['class'] . '.php';
        if (!file_exists($class_path)) {
            throw new \Error('File ' . $class_path . ' does not exist', E_USER_ERROR);
        }
        require $class_path;
    }

    /**
     * 解析类名
     * @param string $class_name 类名
     * @return array 分解后的结果
     */
    private static function parseClassName(string $class_name) : array
    {
        $result = [];
        $body = explode('\\', $class_name);
        $result['class'] = array_pop($body);
        $result['namespace'] = implode('\\', $body);
        $result['namespace_part'] = $body;
        return $result;
    }

    /**
     * 注册自动加载处理方法
     * @return void
     */
    public static function registerAutoload() : void
	{
        spl_autoload_register(array(__CLASS__, 'autoload'), true, true);
    }

    /**
     * 设置命名空间-目录映射
     * @param string $namespace 命名空间
     * @param string $dir 目录
     * @return bool 设置成功与否
     */
    public static function setNamespaceMap(string $namespace, string $dir) : bool
    {
        if (isset(self::$namespace_map[$namespace])) {
            return false;
        }
        self::$namespace_map[$namespace] = $dir;
        return true;
    }

    /**
     *
     * @param string $namespace
     * @return mixed|null
     */
    public static function getNamespaceMap(string $namespace)
    {
        return self::$namespace_map[$namespace] ?? null;
    }
}
