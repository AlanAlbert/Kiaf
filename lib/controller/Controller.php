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
        $tpl_file = APP_TPL_PATH . Config::getValue('jump_tpl');
        if (!file_exists($tpl_file)) {
            throw new \Error('Template file ' . $view_path . ' does not exist!', E_USER_ERROR);
        }
        $tpl = file_get_contents($tpl_file);
        $this->assign('url', $url);
        $this->assign('timeout', $timeout);
        $this->assign('msg', $msg);
        if ($timeout === 0) {
            header("Location:{$url}");
            exit();
        } else {
            $meta = "<meta http-equiv='Refresh' content='[{timeout}];
                URL=[{url}]'>";
            $tpl = $meta . $tpl;
            $tpl = $this->convertView($tpl);
            $cache_file = $this->generateCacheFile($tpl_file, $tpl);
            include $cache_file;
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
     * @return void
     */
    protected function render()
    {
        $view_path = APP_PATH . CURRENT_MODULE . DS .
            'view' . DS .
            CURRENT_CONTROLLER . DS .
            CURRENT_ACTION . '.php';
        if (!file_exists($view_path)) {
            throw new \Error('View file ' . $view_path . ' does not exist!', E_USER_ERROR);
        }
        $view_content = file_get_contents($view_path);
        $view_content = $this->convertView($view_content);
        $cache_path = $this->generateCacheFile($view_path, $view_content);
        include $cache_path;
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
        $content = str_replace($left_delimiter,
            '<?php echo $this->assign_values["', $content);
        $content = str_replace($right_delimiter, '"]; ?>', $content);
        return $content;
    }

    /**
     * 生成静态HTML缓存文件
     * @method generateHtmlCache
     * @return boolean            生成的结果
     */
    private function generateCacheFile($file, $content)
    {
        $modify_time = filemtime($file);
        $cache_file_name = md5($file . '_' . $modify_time) . '.php';
        $cache_path = APP_CACHE_PATH . $cache_file_name;
        if (!file_exists($cache_path)) {
            file_put_contents($cache_path, $content);
        }
        return $cache_path;
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
