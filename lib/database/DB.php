<?php
/**
 * Db.php 数据库相关操作
 * @author: Alan_Albert <alanalbert@qq.com>
 * @version: 0.0.1
 * @copyright: Kiaf
 * @created time: 2018-12-19 20:07:29
 * @last modified by: Alan_Albert <alanalbert@qq.com>
 */
namespace kiaf\database;

class Db
{
    protected static $config = array();
    protected $conn = false;
    protected $sql;

    public function __construct()
    {
        $dns = self::$config['db_type'] . ':' .
            'host=' . self::$config['db_host'] . ';' .
            'port=' . self::$config['db_port'] . ';' .
            'dbname=' . self::$config['db_name'];
        $this->conn = new \PDO($dns,
            self::$config['db_user'],
            self::$config['db_pwd']);
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 插入
     * @method insert
     * @param  string $tbl  表名
     * @param  array  $data 插入的数据
     * @return mixed       受影响的行数或false
     */
    public function insert($tbl, $data = [])
    {
        if (empty($data)) {
            return false;
        }
        $sql = 'insert into `' . $tbl . '` (';
        $insert_data = [];
        $fields = array_keys($data[0]);
        $sql .= $this->generateFields($fields) . ') values ';
        foreach ($data as $value) {
            foreach ($fields as $field) {
                $tmp_value[] = '?';
                $insert_data[] = $value[$field];
            }
            $tmp_values[] = '(' . implode(', ', $tmp_value) . ')';
            unset($tmp_value);
        }
        $sql .= implode(', ', $tmp_values);
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute($insert_data)) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * 查询
     * @method select
     * @param  string  $tbl    表名
     * @param  array   $fields 列
     * @param  array   $where  where条件
     * @param  integer $limit  limit
     * @return mixed          结果数组或false
     */
    public function select($tbl,
        $fields = [],
        $where = '',
        $limit = 0,
        $offset = 0,
        $type = \PDO::FETCH_ASSOC)
    {
        $data = [];
        $sql = 'select ';
        if (empty($fields)) {
            $sql .= '* ';
        } else {
            $sql .= $this->generateFields($fields);
        }
        $sql .= 'from `' . $tbl . '` ';

        if (!empty($where)) {
            $sql .= 'where ' . $where . ' ';
        }

        if ($limit != 0) {
            $sql .= 'limit ' . addslashes($limit) . ' ';
            $sql .= 'offset ' . addslashes($offset) . ' ';
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute($data)) {
            return $stmt->fetchAll($type);
        }
        return false;
    }

    /**
     * 删除
     * @method delete
     * @param  string  $tbl   表名
     * @param  string  $where where条件
     * @param  integer $limit limit
     * @return mixed         删除的函数或false
     */
    public function delete($tbl, $where = '', $limit = 0)
    {
        $sql = 'delete from `' . $tbl . '` ';
        if ($where !== '') {
            $sql .= 'where ' . $where . ' ';
        }
        if ($limit !== 0) {
            $sql .= 'limit ' . addslashes($limit) . ' ';
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute()) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * 更新
     * @method update
     * @param  string  $tbl   表名
     * @param  array   $data  更新的数据
     * @param  string  $where where条件
     * @param  integer $limit limit
     * @return mixed         受影响行数或false
     */
    public function update($tbl, $data = [], $where = '', $limit = 0)
    {
        if (empty($data)) {
            return false;
        }
        $update_data = [];
        $sql = 'update `' . $tbl . '` set ';
        foreach ($data as $key => $value) {
            $tmp[] = $key . '=:' . $key;
            $update_data[':' . $key] = $value;
        }
        $sql .= implode(', ', $tmp) . ' ';
        if ($where !== '') {
            $sql .= 'where ' . $where . ' ';
        }
        if ($limit !== 0) {
            $sql .= 'limit ' . addslashes($limit) . ' ';
        }
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute($update_data)) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * 返回上一次操作的错误信息
     * @method getLastError
     * @return array       错误信息
     */
    public function getLastError()
    {
        return array(
            'errorCode' => $this->conn->errorCode(),
            'errorInfo' => $this->conn->errorInfo()
        );
    }

    /**
     * 直接执行SQL语句
     * @method execSql
     * @param  string  $sql SQL语句
     * @return array       结果
     */
    public function execSql($sql, $type = \PDO::FETCH_ASSOC)
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute()) {
            return $stmt->fetchAll($type);
        }
        return [];
    }

    /**
     * 生成SQL的列
     * @method generateFields
     * @param  array         $fields 列
     * @return string                 部分SQL
     */
    private function generateFields($fields)
    {
        $sql = '';
        $tmp = [];
        foreach ($fields as $value) {
            $tmp[] = '`' . $value . '`';
        }
        $sql .= implode(', ', $tmp) . ' ';
        return $sql;
    }

    /**
     * 设置数据库配置
     * @method setConfig
     * @param  array    $config 设置数据库配置
     */
    public static function setConfig($config)
    {
        self::$config = $config;
    }
}
