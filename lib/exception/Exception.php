<?php
/**
 * Exception.php 错误处理
 * @author: Alan_Albert <alanalbert@qq.com>
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-22 13:05:25
 * @last modified by: Alan_Albert <alanalbert@qq.com>
 */
namespace kiaf\exception;

class Exception
{
    /**
     * 注册异常处理函数
     * @method registerExceptionHandler
     * @return void
     */
    public static function registerExceptionHandler()
    {
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
    }

    /**
     * 异常处理回调函数
     * @method exceptionHandler
     * @param  \Throwable           $e 异常或错误
     * @return void
     */
    public static function exceptionHandler(\Throwable $e)
    {
        // TODO
        // 使用错误模板
        if (DEBUG_MODE) {
            var_dump($e);
        } else {
            echo '页面发生错误，请稍后再试';
        }
    }
}
