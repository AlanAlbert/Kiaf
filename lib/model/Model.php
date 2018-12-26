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
    private $db;        // Db类
    private $table;     // 当前表名
    private $tbl_fields = array();  // 表的所有列
    private $tbl_primary;           // 表的主键
    private $where = '';            // where条件
    private $limit = 0;             // limit条件
    private $offset = 0;            // offset条件
    private $type = \PDO::FETCH_ASSOC;  // 返回的数据类型
    private $fields = [];               // 查询的列

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
        $this->tbl_fields = $fields['fields'];
        $this->tbl_primary = $fields['primary'];
    }

    /**
     * 获取当前表的记录数
     * @return mixed
     */
    public function count()
    {
        return $this->db->count($this->table)[0]['count'];
    }

    /**
     * where条件
     * @param string $where where条件
     * @param array $params 参数
     * @return $this
     */
    public function where(string $where, array $params = []) : Model
    {
        if (empty($params)) {
            $this->where = $where;
        } else {
            foreach ($params as $key => $param) {
                $params[$key] = addslashes($param);
            }
            array_unshift($params, $where);
            $this->where = call_user_func_array('sprintf', $params);
        }
        return $this;
    }

    /**
     * limit条件
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit) : Model
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * offset条件
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset) : Model
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * 返回数据的类型 \PDO::FETCH_ASSOC ...
     * @param int $type
     * @return $this
     */
    public function type(int $type) : Model
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 查询数据的列
     * @param array $fields
     * @return $this
     */
    public function field(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * 查询
     * @return array|bool 结果或false
     */
    public function select()
    {
        $result = $this->db->select($this->table,
            $this->fields,
            $this->where,
            $this->limit,
            $this->offset,
            $this->type);
        $this->resetValue();
        return $result;
    }

    /**
     *
     * @param array $data 待插入的数据
     * @return int|bool 插入的行数或false
     */
    public function insert(array $data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * 删除
     * @return mixed
     */
    public function delete()
    {
        $result = $this->db->delete($this->table,
            $this->where,
            $this->limit);
        $this->resetValue();
        return $result;
    }

    /**
     * 更新
     * @param int $data 更新的数据
     * @return mixed
     */
    public function update(array $data)
    {
        $result = $this->db->update($this->table,
            $data,
            $this->where,
            $this->limit);
        $this->resetValue();
        return $result;
    }

    /**
     * 获取当前表的列
     * @return array|mixed
     */
    public function getField()
    {
        return $this->tbl_fields;
    }

    /**
     * 获取主键
     * @return mixed
     */
    public function getPrimary()
    {
        return $this->tbl_primary;
    }

    /**
     * 重置limit、where等值
     * @return $this
     */
    private function resetValue()
    {
        $this->limit = $this->offset = 0;
        $this->where = '';
        $this->fields = [];
        $this->type = \PDO::FETCH_ASSOC;
        return $this;
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
