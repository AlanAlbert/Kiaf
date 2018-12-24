<?php
/**
 * Model.php 模型基类
 * @author: Alan_Albert <alanalbert@qq.com>
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-19 18:51:28
 * @last modified by: Alan_Albert <alanalbert@qq.com>
 */
namespace kiaf\model;

use kiaf\database\Db;

class Model
{
    private $db;
    private $table;
    private $fields = array();
    private $primary;

    public function __construct()
    {
        # 根据模型类名加载表名
        $class = substr(static::class,
            strrpos(static::class, '\\') + 1);
        $this->table = $this->humpToLine($class);

        # 数据库连接
        $this->db = new Db();

        # 获取当前表所有列
        $fields = $this->getFields();
        $this->fields = $fields['fields'];
        $this->primary = $fields['primary'];
    }


    /**
     * 获取列
     * @method getFields
     * @return array    ['fields' => [], 'primary' => []]
     */
    private function getFields() : array
    {
        $result = [];
        $sql = 'desc ' . $this->table;
        $fields = $this->db->querySql($sql);
        foreach ($fields as $key => $value) {
            $result['fields'][] = $value['Field'];
            if ($value['Key'] === 'PRI') {
                 $result['primary'][] = $value['Field'];
            }
        }
        return $result;
    }

    /**
     * 大驼峰转下划线
     * @method humpToLine
     * @param  string     $str 大驼峰式名
     * @return string          下划线连接式名
     */
    private function humpToLine(string $str) : string
    {
        $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '_'.strtolower($matches[0]);
        },$str);
        return substr($str, 1);
    }
}
