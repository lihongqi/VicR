<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/14
 * Time: 下午3:18
 */

namespace Drm;

class Db
{
    private $pdo = null;

    private $start_time = 0;

    private $sql_info = [];

    private $sql_data = [];

    private function __construct()
    {

    }

    /**
     * @param \PDOStatement $PDOStatement
     * @return mixed
     */
    private function fetch($PDOStatement)
    {
        return $PDOStatement->fetch(\PDO::FETCH_ASSOC);
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
     * @param \PDOStatement $PDOStatement
     * @return array
     */
    private function fetchAll($PDOStatement)
    {
        return $PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
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
        \Log::debug([$sql, $arr], 3);
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
                throw new \Except\DbError('execute failed ' . $sql . ' ' . json_encode($arr));
            }
        } else {
            return $this->getPdo()->query($sql);
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
            $s .= ' LEFT JOIN ' . $k . ' on ' . $v;
        }
        return $s;
    }

    private function _table($str)
    {
        return ' `' . $str . '` ';
    }

    /**
     * @param array $data
     * @return string
     */
    private function _where($data)
    {
        if (isset($data[0])) {
            $ws = [];
            foreach ($data as $w) {
                $ws[] = $this->_data($w, ' AND ');
            }
            $where = '(' . implode(' OR ', $ws) . ')';
        } else {
            $where = $this->_data($data, ' AND ');
        }
        $whs = implode(' AND ', $this->whs);
        if ($whs && $where) {
            return ' WHERE ' . $whs . ' AND ' . $where;
        } else if ($whs || $where) {
            return ' WHERE ' . $whs . $where;
        }
        return '';
    }

    private $whs = [];

    /**
     * @param string $field
     * @param string $sign
     * @param string $val
     * @return $this
     */
    public function where($field, $sign, $val)
    {
        $this->sql_data[] = $val;
        $this->whs = array_merge($this->whs, [$field . ' ' . $sign . ' ' . '?']);
        return $this;
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
            return $dbs[$key];
        } else {
            $ot = self::createPDO($key);
            $dbs[$key] = $ot;
            return $dbs[$key];
        }
    }

    /**
     * @param string $key
     * @return DB
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
            throw new \Except\DbError('connection failed dns:' . $key);
        }
        $ot = new self;
        $ot->pdo = $db;
        $ot->start_time = microtime(true);
        return $ot;
    }


}