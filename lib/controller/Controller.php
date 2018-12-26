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
     * @param  int $timeout 延时
     * @param  string  $msg     提示信息
     * @return void
     */
    protected function jump(string $url, int $timeout = 2, string $msg = '跳转中，请稍后...') : void
    {
        $tpl_file = APP_TPL_PATH . Config::getValue('jump_tpl');
        if (!file_exists($tpl_file)) {
            throw new \Error('Template file does not exist!' . $tpl_file, E_USER_ERROR);
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
     * @param  string $key   键
     * @param  mixed $value 值
     * @return void
     */
    protected function assign(string $key, $value) : void
    {
        $this->assign_values[$key] = $value;
    }

    /**
     * 渲染视图
     * @method render
     * @return void
     */
    protected function render() : void
    {
        $view_path = APP_PATH . CURRENT_MODULE . DS .
            'view' . DS .
            strtolower(CURRENT_CONTROLLER) . DS .
            CURRENT_ACTION . '.php';
        if (!file_exists($view_path)) {
            throw new \Error('View file does not exist!' . $view_path, E_USER_ERROR);
        }
        $view_content = file_get_contents($view_path);
        $cache_path = $this->generateCacheFile($view_path, $view_content);
        include $cache_path;
    }

    /**
     * 转换变量
     * @method convertView
     * @param  string    $content 转换
     * @return string             转换完的内容
     */
    private function convertView(string $content) : string
    {
        require_once LIBRARY_PATH . 'phpquery/phpQuery.php';
        libxml_use_internal_errors(true);
        $left_delimiter = Config::getValue('left_delimiter');
        $right_delimiter = Config::getValue('right_delimiter');

        while (true){
            $doc = \phpQuery::newDocumentHTML($content);
            $fors = $doc['for'];
            $ifs = $doc['if'];
            $elseifs = $doc['elseif'];
            $elses = $doc['else'];
            if (!count($fors) &&
                !count($ifs) &&
                !count($elseifs) &&
                !count($elses)) break;
            // 转换if标签
            foreach ($ifs as $if) {
                $if = pq($if);
                if (!$condition = $if->attr('condition')) {
                    throw new \Error('View dose not have attribute "condition"',
                        E_USER_ERROR);
                }
                $condition = $this->convertEquJudge($condition);
                $inner_html = urldecode($if->html());
                $replace = '<?php if(' . $condition . '){ ?>';
                $replace .= $inner_html;
                $replace .= '<?php }?>';
                $if->replaceWith($replace);
            }
            // 转换elseif标签
            foreach ($elseifs as $elseif) {
                $elseif = pq($elseif);
                if (!$condition = $elseif->attr('condition')) {
                    throw new \Error('View dose not have attribute "condition"',
                        E_USER_ERROR);
                }
                $condition = $this->convertEquJudge($condition);
                $replace = '<?php }elseif(' . $condition . '){ ?>';
                $elseif->replaceWith($replace);
            }
            // 转换else标签
            foreach ($elses as $else) {
                $else = pq($else);
                $replace = '<?php }else{ ?>';
                $else->replaceWith($replace);
            }
            // 装换for标签
            foreach ($fors as $for) {
                $for = pq($for);
                if (!$data = $for->attr('data')) {
                    throw new \Error('View dose not have attribute "data"',
                        E_USER_ERROR);
                }

                $key = $for->attr('key') ?? 'key';
                $value = $for->attr('value') ?? 'value';

                $inner_html = urldecode($for->html());
                $replace = '<?php foreach($this->assign_values["' . $data . '"] as $' . $key .
                    ' => $' . $value . '){ ?>';
                $pattern_key = '/' . addcslashes($left_delimiter . $key . $right_delimiter, '{[]}') . '((\[.*?\])*)/is';
                $pattern_value = '/' . addcslashes($left_delimiter . $value . $right_delimiter, '{[]}') . '((\[.*?\])*)/is';
                $inner_html = preg_replace_callback_array([
                    $pattern_key => function ($matches) use ($key) {
                        return '<?php echo $' . $key . $matches[1] . ';?>';
                    },
                    $pattern_value => function ($matches) use ($value) {
                        return '<?php echo $' . $value . $matches[1] . ';?>';
                    }
                ], $inner_html);
                $replace .= $inner_html;
                $replace .= '<?php } ?>';
                $for->replaceWith($replace);
            }
            $content = urldecode(html_entity_decode($doc->htmlOuter()));
        }
        $pattern = '/' . addcslashes($left_delimiter, '{[]}') .
            '(.*?)' .addcslashes($right_delimiter, '{[]}') .
            '((\[.*?\])*)/is';
        $content = preg_replace_callback($pattern, function ($matches) {
            return '<?php echo $this->assign_values["' . $matches[1] . $matches[2] .'"]; ?>';
        }, $content);
        return $content;
    }

    /**
     * 模板中判断条件 lt mt eq le me 的转换
     * @param string $condition 条件
     * @return string    转换后的条件
     */
    private function convertEquJudge(string $condition) : string
    {
        $condition = str_replace('lt', '<', $condition);
        $condition = str_replace('mt', '>', $condition);
        $condition = str_replace('eq', '==', $condition);
        $condition = str_replace('le', '<=', $condition);
        $condition = str_replace('me', '>=', $condition);
        $condition = str_replace(Config::getValue('left_delimiter'), '$this->assign_values["', $condition);
        $condition = str_replace(Config::getValue('right_delimiter'), '"]', $condition);
        return $condition;
    }

    /**
     * 生成静态缓存文件
     * @method generateHtmlCache
     * @param    string   $file    文件路径
     * @param string   $content 内容
     * @return  string            生成的文件路径
     */
    private function generateCacheFile(string $file, string $content) : string
    {
        $modify_time = filemtime($file);
        $cache_file_name = md5($file . '_' . $modify_time) . '.php';
        $cache_path = APP_CACHE_PATH . $cache_file_name;
        if (!file_exists($cache_path)) {
            $content = $this->convertView($content);
            file_put_contents($cache_path, $content);
        }
        return $cache_path;
    }

    /**
     * 根据模块、控制器、方法生成URL
     * @method generateUrl
     * @param  array       $params     URL的参数
     * @param  string      $action     方法名
     * @param  string      $controller 控制器名
     * @param  string      $module     模块名
     * @return string                  URL
     */
    protected function generateUrl(array $params,
        string $action = CURRENT_ACTION,
        string $controller = CURRENT_CONTROLLER,
        string $module = CURRENT_MODULE) : string
    {
        $url = ROOT_URL . "?m={$module}&c={$controller}&a={$action}";
        foreach ($params as $key => $value) {
            $url .= "&{$key}={$value}";
        }
        return $url;
    }
}
