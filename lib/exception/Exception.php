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
    public static function registerExceptionHandler() : void
    {
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
    }

    /**
     * 异常处理回调函数
     * @method exceptionHandler
     * @param  \Throwable           $e 异常或错误
     * @return void
     */
    public static function exceptionHandler(\Throwable $e) :void
    {
        // TODO
        // 使用错误模板
        
        ob_end_clean();
        if (DEBUG_MODE) {
            echo '<h2>', $e->getMessage(), '</h2>';
            echo '<h3>File: <span style="color:red;">', $e->getFile(), '</span></h3>';
            echo '<h3>Line: <span style="color:red;">', $e->getLine(), '</span></h3>';
            echo '<h3>Trance: <span style="color:red;">', $e->getTraceAsString(), '</span></h3>';
        } else {
            echo '<h1>页面发生错误，请稍后再试~</h1>';
        }
        exit();
    }
}
