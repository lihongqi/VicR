<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/14
 * Time: 下午3:18
 */

namespace Drm;

use Cache\File;

class Db
{
    private $pdo = null;

    private $start_time = 0;

    private $sql_info = [];

    private $sql_data = [];

    private $max_connect_time = 600;

    private function __construct()
    {

    }


    /**
     * @return string
     */
    private function lastInsertId()
    {
        return $this->getPdo()->lastInsertId();
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        if ($this->inTransaction()) {
            return true;
        }
        return $this->getPdo()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        if ($this->inTransaction()) {
            return $this->getPdo()->rollBack();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function commit()
    {
        if ($this->inTransaction()) {
            return $this->getPdo()->commit();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->getPdo()->inTransaction();
    }

    /**
     * @var array [
     *    key => ['self.field',table,table.field,data]
     * ]
     */
    private $_with = [];

    /**
     * @param string $key
     * @param array $with [table,self_link_key,link_key,ops,[next=[with]]]
     */
    public function with($key, $with)
    {
        if (!isset($with['ops'])) {
            $with['ops'] = [];
        }
        $this->_with[$key] = $with;
    }

    private function getWith($data)
    {
        $with = $this->_with;
        $this->_with = [];
        foreach ($with as $k => $v) {
            $data = $this->getWithData($data, $v, $k);
        }
        return $data;
    }

    private function getWithData($data, $with, $field)
    {
        $ids = [];
        foreach ($data as $val) {
            $ids[] = $val[$with['self_link_key']];
        }

        $with['ops']['where'][] = [$with['link_key'], 'in', $ids];
        $arr = $this->findAll([
                'table' => $with['table']
            ] + $with['ops']);

        if (isset($with['next'])) {
            $arr = $this->getWithData($arr, $with['next'], 'next');
        }

        $ar = [];
        foreach ($arr as $v) {
            if (isset($with['multi'])) {
                $ar[$v[$with['link_key']]][] = $v;
            } else {
                $ar[$v[$with['link_key']]] = $v;
            }
        }

        foreach ($data as &$val) {
            if (isset($ar[$val[$with['self_link_key']]])) {
                $val[$field] = $ar[$val[$with['self_link_key']]];
            } else {
                $val[$field] = null;
                \Log::warn("关联不存在 {$with['self_link_key']} => {$val[$with['self_link_key']]}");
            }
        }

        return $data;
    }

    /**
     * @param \PDOStatement $PDOStatement
     * @return mixed
     */
    private function fetch($PDOStatement)
    {
        $res = $PDOStatement->fetch(\PDO::FETCH_ASSOC);
        if ($this->_with && $res) {
            $res = $this->getWith([$res]);
            $res = $res[0];
        }
        return $res;
    }

    /**
     * @param \PDOStatement $PDOStatement
     * @return array
     */
    private function fetchAll($PDOStatement)
    {
        $res = $PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
        if ($this->_with) {
            if ($res) {
                $res = $this->getWith($res);
            } else {
                $this->_with = [];
            }
        }
        return $res;
    }

    /**
     * @param \PDOStatement $PDOStatement
     * @return mixed
     */
    private function rowCount($PDOStatement)
    {
        return $PDOStatement->rowCount();
    }

    /**
     * @param $sql
     * @return \PDOStatement
     */
    private function prepare($sql, $ops = [])
    {
        return $this->getPdo()->prepare($sql, $ops);
    }

    /**
     * @param string $sql
     * @param array $arr
     * @return array
     */
    public function query($sql, $arr = [])
    {
        return $this->fetchAll($this->execute($sql, $arr));
    }

    /**
     * @param string $sql
     * @param array $arr
     * @return int
     */
    public function exec($sql, $arr = [])
    {
        return $this->rowCount($this->execute($sql, $arr));
    }


    /**
     * @param string $sql
     * @param array $arr
     * @return \PDOStatement
     */
    private function execute($sql, $arr = [])
    {
//        \Log::debug([$sql, $arr], 4);
        if ($arr) {
            $this->sql_data = $this->whs = [];
            if (!isset($arr[0])) {
                $ops = [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL];
            } else {
                $ops = [];
            }
            $pt = $this->prepare($sql, $ops);
            if ($pt->execute($arr)) {
                return $pt;
            } else {
                throw new \Except\DbError('execute failed ' . $sql . ' ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
            }
        } else {
            $res = $this->getPdo()->query($sql);
            if ($res) {
                return $res;
            } else {
                throw new \Except\DbError('execute failed ' . $sql . ' ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * @param string|array $info
     */
    public function find($info)
    {
        $this->_sql($info);
        return $this->fetch($this->execute($this->sql_info[0], $this->sql_info[1]));

    }

    /**
     * @param string|array $info
     */
    public function findAll($info)
    {
        $this->_sql($info);
        return $this->fetchAll($this->execute($this->sql_info[0], $this->sql_info[1]));
    }

    /**
     * @param string|array $info
     */
    public function update($info)
    {
        $this->_sql($info, 2);
        return $this->rowCount($this->execute($this->sql_info[0], $this->sql_info[1]));

    }

    /**
     * @param string|array $info
     */
    public function insert($info)
    {
        $this->_sql($info, 3);
        $this->execute($this->sql_info[0], $this->sql_info[1]);
        return $this->lastInsertId();

    }

    /**
     * @param string|array $info
     */
    public function delete($info)
    {
        $this->_sql($info, 1);
        return $this->rowCount($this->execute($this->sql_info[0], $this->sql_info[1]));

    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @return array
     */
    private function getOps()
    {
        return array(
            'table' => '',
            'field' => '*',
            'where' => [],
            'left' => [],
            'limit' => '',
            'group' => '',
            'order' => '',
            'data' => []
        );
    }

    /**
     * @param string|array $data
     * @param int $d
     * @return bool
     */
    private function _sql($data, $d = 0)
    {
        if (is_string($data)) {
            $this->sql_info = [$data, []];
            return true;
        }
        $data = $data + $this->getOps();
        $sql = '';
        $data['table'] = $this->_table($data['table']);
        if ($d == 0) {
            $sql = 'SELECT ' . $data['field'] . ' FROM ' . $data['table'] .
                $this->_left($data['left']) .
                $this->_where($data['where']) .
                $this->_group($data['group']) .
                $this->_order($data['order']) .
                $this->_limit($data['limit']);
        } elseif ($d == 1) {
            $sql = 'DELETE FROM ' . $data['table'] . $this->_where($data['where']);
        } elseif ($d == 2) {
            $sql = 'UPDATE ' . $data['table'] . ' SET ' . $this->_data($data['data']) . $this->_where($data['where']);
        } elseif ($d == 3) {
            list($key, $val) = $this->_dataInsert($data['data']);
            $sql = 'INSERT INTO' . $data['table'] . $key . ' VALUES ' . $val;
        }
        $this->sql_info = [$sql, $this->sql_data];
        return true;
    }

    /**
     * @param array $ar
     * @return string
     */
    private function _left($ar)
    {
        $s = '';
        foreach ($ar as $k => $v) {
            $s .= ' LEFT JOIN ' . $this->_table($k) . ' on ' . $v;
        }
        return $s;
    }

    private function _table($str)
    {
        $r = explode(' ', $str);
        $r = array_filter($r, function ($v) {
            if ($v) {
                return $v;
            }
        });
        $table = $r[0];
        unset($r[0]);
        return ' `' . $table . '` ' . implode(' ', $r);
    }

    /**
     * @param array $data
     * @return string
     */
    private function _where($data)
    {
        $w = [];
        foreach ($data as $k => $v) {
            if (is_numeric($k)) {
                if(isset($v[0])){
                    $w[] = $this->where($v[0],$v[1],$v[2]);
                }else{
                    $w[] = '(' . $this->_data($v, ' OR ') . ')';
                }
                unset($data[$k]);
            }
        }
        if($data){
            $w[] = $this->_data($data,' AND ');
        }
        $where = implode(' AND ',$w);
        if($where){
            return ' WHERE '.$where;
        }
        return  '';

    }

    /**
     * @param string $field
     * @param string $sign
     * @param string|array $val
     * @return string
     */
    private function where($field, $sign, $val)
    {
        $whs = [];
        $sign = strtolower($sign);
        if ($sign == 'in' || $sign == 'not in') {
            $h = [];
            foreach ($val as $v) {
                $this->sql_data[] = $v;
                $h[] = '?';
            }
            $whs = $field . ' ' . $sign . ' ' . '(' . implode(',', $h) . ')';
        } else {
            $this->sql_data[] = $val;
            $whs = $field . ' ' . $sign . ' ' . '?';
        }
        return $whs;
    }

    private function _order($str)
    {
        $s = '';
        if ($str) {
            $s = ' ORDER BY ' . $str;
        }
        return $s;
    }

    private function _group($str)
    {
        $s = '';
        if ($str) {
            $s = ' GROUP BY ' . $str;
        }
        return $s;
    }

    private function _limit($str)
    {
        $s = '';
        if ($str) {
            $s = ' LIMIT ' . $str;
        }
        return $s;
    }

    private function _dataInsert($data)
    {
        $key = [];
        $val = [];
        $arr = [];
        $_k = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                list($_k, $_v) = $this->_dataInsert($v);
                $arr[] = $_v;
            } else {
                $key[] = "`{$k}`";
                $val[] = '?';
            }
        }
        if ($arr) {
            return [$_k, implode(',', $arr)];
        } else {
            $this->sql_data = array_merge($this->sql_data, array_values($data));
            return ['(' . implode(',', $key) . ')', '(' . implode(',', $val) . ')'];
        }
    }

    private function _data($data, $p = ',')
    {
        $rr = [];
        foreach ($data as $k => $v) {
            $rr[] = "{$k} = ?";
        }
        $this->sql_data = array_merge($this->sql_data, array_values($data));
        return implode($p, $rr);
    }

    /**
     * @param string $key
     * @return self
     */
    public static function init($key = 'default')
    {
        static $dbs = [];

        if (isset($dbs[$key])) {
            if ($dbs[$key]->start_time + $dbs[$key]->max_connect_time > time()) {
                return $dbs[$key];
            } else {
                unset($dbs[$key]);
            }
        }

        $ot = self::createPDO($key);
        $dbs[$key] = $ot;
        return $dbs[$key];
    }

    /**
     * @param string $key
     * @return Db
     * @throws \Except\DbError
     */
    private static function createPdo($key)
    {
        $conf = \App::config('Db.' . $key);
        if (!$conf) {
            throw new \Except\DbError('not find dns:' . $key);
        }
        try {
            $db = new \PDO($conf['dns'], $conf['username'], $conf['password'], $conf['ops']);
        } catch (\PDOException $e) {
            throw new \Except\DbError('connection failed dns:' . $key . ' ' . $e->getMessage());
        }
        $ot = new self;
        $ot->pdo = $db;
        $ot->start_time = time();
        return $ot;
    }

    /**
     * 生成表结构缓存
     */
    private function createDbFieldCache()
    {
        $db = self::init();
        $tables = $db->findAll('show tables');
        $rr = [];
        foreach ($tables as $v) {
            $table = end($v);
            $res = $db->findAll("desc $table");
            $fr = array();
            foreach ($res as $fv) {
                $pos = strpos($fv['Type'], '(');
                if (!$pos) {
                    $pos = strpos($fv['Type'], ' ');
                }
                if ($pos) {
                    $type = substr($fv['Type'], 0, $pos);
                } else {
                    $type = $fv['Type'];
                }
                $fr[$fv['Field']] = $type;
            }
            $rr[$table] = $fr;
        }
        return $rr;
    }

    private static $fields = [];

    /**
     * @return array
     */
    public function getDbField($fresh_cache = false)
    {
        if (!self::$fields) {
            $res = \Cache\File::get('db_field');
            if ($res == false || $fresh_cache == true) {
                $res = $this->createDbFieldCache();
                File::set('db_field', $res, 36000);
            }
            self::$fields = $res;
        }
        return self::$fields;

    }

}