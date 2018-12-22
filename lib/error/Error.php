<?php
/**
 * Error.php 错误处理
 * @author: Alan_Albert <alanalbert@qq.com>
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-22 00:44:29
 * @last modified by: Alan_Albert <alanalbert@qq.com>
 */
namespace kiaf\error;

class Error
{
    /**
     * 注册错误处理函数
     * @method registerErrorHandler
     * @return void
     */
    public static function registerErrorHandler()
    {
        error_reporting(0);
        set_error_handler(array(__CLASS__, 'errorHandler'));
        register_shutdown_function(array(__CLASS__, 'shutdownHandler'));
    }

    /**
     * set_error_handler的回调函数
     * @method errorHandler
     * @param  int       $err_type 错误类型
     * @param  string        $err_msg  错误消息
     * @param  string       $err_file 错误文件
     * @param  int       $err_line 错误行号
     * @return void
     */
    public static function errorHandler($err_type, $err_msg, $err_file, $err_line)
    {
        var_dump($err_type);
        var_dump($err_msg);
        var_dump($err_file);
        var_dump($err_line);
    }

    /**
     * register_shutdown_function的回调函数
     * @method shutdownHandler
     * @return void
     */
    public static function shutdownHandler()
    {
        if ($error = error_get_last()) {
            var_dump($error);
        }
    }
}
