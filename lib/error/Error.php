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
        // TODO
        // 使用错误模板

        ob_end_clean();
        if (DEBUG_MODE) {
            echo '<h2>', $err_msg, '</h2>';
            echo '<h3>File: <span style="color:red;">', $err_file, '</span></h3>';
            echo '<h3>Line: <span style="color:red;">', $err_line, '</span></h3>';
        } else {
            echo '<h2>页面发生错误，请稍后再试~</h2>';
        }
        exit();
    }

    /**
     * register_shutdown_function的回调函数
     * @method shutdownHandler
     * @return void
     */
    public static function shutdownHandler()
    {
        if ($error = error_get_last()) {
            // TODO
            // 使用错误模板
            
            ob_end_clean();
            if (DEBUG_MODE) {
                echo '<h2>', $error['message'], '</h2>';
                echo '<h3>File: <span style="color:red;">', $error['file'], '</span></h3>';
                echo '<h3>Line: <span style="color:red;">', $error['line'], '</span></h3>';
            } else {
                echo '<h1>页面发生错误，请稍后再试</h1>';
            }
            exit();
        }
    }
}
