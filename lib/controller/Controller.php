<?php
/**
 * Controller 控制器基类
 * @author: Alan_Albert <alanalbert@qq.com>
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-19 18:50:14
 * @last modified by: Alan_Albert <alanalbert@qq.com>
 */
namespace kiaf\controller;

use kiaf\config\Config;

class Controller
{
    protected $assign_values = array();

    /**
     * 跳转函数
     * @method jump
     * @param  string  $url     跳转的URL
     * @param  integer $timeout 延时
     * @param  string  $msg     提示信息
     * @return void
     */
    protected function jump($url, $timeout = 2, $msg = '跳转中，请稍后...')
    {
        $tpl = file_get_contents(APP_TPL_PATH . Config::getValue('jump_tpl'));
        $this->assign('url', $url);
        $this->assign('timeout', $timeout);
        $this->assign('msg', $msg);
        if (headers_sent()) {
            $meta = "<meta http-equiv='Refresh' content='{$timeout};
                URL=[{url}]'>";
            $tpl = $meta . $tpl;
            $this->render($tpl);
            exit();
        } else {
            if ($timeout === 0) {
                header("Location:{$url}");
            } else {
                header("refresh:{$timeout};url={$url}");
                $this->render($tpl);
            }
            exit();
        }
    }

    /**
     * 指定需要渲染的值
     * @method assign
     * @param  mixed $key   键
     * @param  mixed $value 值
     * @return void
     */
    protected function assign($key, $value)
    {
        $this->assign_values[$key] = $value;
    }

    /**
     * 渲染视图
     * @method render
     * @param string content 渲染的内容
     * @return void
     */
    protected function render($view_content = null)
    {
        if (!$view_content) {
            $view_content = file_get_contents(APP_PATH . CURRENT_MODULE . DS .
                'view' . DS .
                CURRENT_CONTROLLER . DS .
                CURRENT_ACTION . '.php');
        }
        $view_content = $this->convertView($view_content);
        // TODO
        // 调用generateHtmlCache()生成静态缓存文件
        echo $view_content;
    }

    /**
     * 转换变量
     * @method convertView
     * @param  string    $content 转换
     * @return string             转换完的内容
     */
    private function convertView($content)
    {
        $left_delimiter = Config::getValue('left_delimiter');
        $right_delimiter = Config::getValue('right_delimiter');
        foreach ($this->assign_values as $key => $value) {
            $content = str_replace($left_delimiter . $key . $right_delimiter,
                $value, $content);
        }
        return $content;
    }

    /**
     * 生成静态HTML缓存文件
     * @method generateHtmlCache
     * @return boolean            生成的结果
     */
    private function generateHtmlCache()
    {
        // TODO
        // 生成静态HTML缓存文件
    }

    /**
     * 根据模块、控制器、方法生成URL
     * @method generateUrl
     * @param  string      $action     方法名
     * @param  string      $controller 控制器名
     * @param  string      $module     模块名
     * @return string                  URL
     */
    protected function generateUrl($params,
        $action = CURRENT_ACTION,
        $controller = CURRENT_CONTROLLER,
        $module = CURRENT_MODULE)
    {
        $url = ROOT_URL . "?m={$module}&c={$controller}&a={$action}";
        foreach ($params as $key => $value) {
            $url .= "&{$key}={$value}";
        }
        return $url;
    }
}
