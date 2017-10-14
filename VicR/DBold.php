<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/14
 * Time: 上午10:55
 */
class DBold
{

    public $db = null; //原生PDO对象 外部可以直接调用

    protected static $logs = array(); //日志
    protected static $ops = array(); //默认参数
    protected $sql = ''; //当前sql
    protected $start_time = 0;
    protected $end_time = 0;

    protected $PDO_fetch = PDO::FETCH_ASSOC;

    public function __destruct()
    {
        $r = 3;
        $sqlr = $this->get_logs();

        if ($sqlr != null) {
            foreach ($sqlr as $v) {
                if ($v['info'][0] != '00000') {
                    $r = 1;
                    break;
                }
            }
            Log::mlog($sqlr, $r);
            $this->clear_logs();
        }

        if ($this->inTransaction()) {
            if ($r == 1) {
                $this->rollBack();
            } else {
                $this->commit();
            }
        }
    }

    public function get_logs()
    {
        return self::$logs;
    }

    public function clear_logs()
    {
        self::$logs = null;
    }

    /**
     * 慢sql
     * @param $level_time 临界点，单位毫秒
     **/
    public function get_slow_logs($level_time)
    {
        $slow_logs = array();
        foreach (self::$logs as $log) {
            if (isset($log['time']) && ($log['time'] > $level_time))
                $slow_logs[] = $log;
        }
        return $slow_logs;
    }

    /*
    * ms 保留一位小数
    */
    private function getExeTime()
    {
        return round(($this->end_time - $this->start_time) * 1000, 1);
    }

    public function query($sql)
    {
        $this->start_time = microtime(true);
        $rs = $this->db->query($sql);
        $this->end_time = microtime(true);
        $this->_setsql(array(
            'sql' => $this->sql,
            'time' => $this->getExeTime(),
            'info' => $this->db->errorInfo()
        ));
        return $rs;
    }

    public function exec($sql)
    {
        $this->start_time = microtime(true);
        $rs = $this->db->exec($sql);
        $this->end_time = microtime(true);
        $this->_setsql(array(
            'sql' => $sql,
            'time' => $this->getExeTime(),
            'info' => $this->db->errorInfo()
        ));
        return $rs;
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function beginTransaction()
    {
        if (!$this->inTransaction()) {
            $this->db->beginTransaction();
            $this->_setsql(array(
                'sql' => 'START TRANSACTION',
                'info' => $this->db->errorInfo()
            ));
        }
    }

    public function rollBack()
    {
        if ($this->inTransaction()) {
            $this->db->rollBack();
            $this->_setsql(array(
                'sql' => 'ROLLBACK',
                'info' => $this->db->errorInfo()
            ));
        }
    }

    public function commit()
    {
        if ($this->inTransaction()) {
            $this->db->commit();
            $this->_setsql(array(
                'sql' => 'COMMIT',
                'info' => $this->db->errorInfo()
            ));
        }
    }

    public function inTransaction()
    {
        return $this->db->inTransaction();
    }

    /**
     *
     * 获取一条数据
     * @param $ops 参数
     * @param $r 是否继承上次设置的参数
     *
     * */
    public function find($ops = array())
    {
        $this->_sql($ops);
        $res = $this->query($this->sql);
        if ($res) {
            return $res->fetch($this->PDO_fetch);
        } else {
            return [];
        }
    }

    public function findAll($ops = array())
    {
        $this->_sql($ops);
        $res = $this->query($this->sql);
        if ($res) {
            return $res->fetchAll($this->PDO_fetch);
        } else {
            return [];
        }
    }

    public function delete($ops = array())
    {
        $this->_sql($ops, 1);
        return $this->exec($this->sql);
    }

    public function update($ops = array())
    {
        $this->_sql($ops, 2);
        return $this->exec($this->sql);
    }

    public function insert($ops = array())
    {
        $this->_sql($ops, 3);
        $rs = $this->exec($this->sql);
        $id = $this->lastInsertId();
        if ($rs !== false) {
            return $id;
        } else {
            return false;
        }

    }

    protected static function getarg()
    {
        $ops = array(
            'conn' => 'default',
            'table' => '',
            'field' => '*',
            'where' => '', //string or array('id'=>1)
            'left' => array(),
            'limit' => '',
            'group' => '',
            'order' => '',
            'data' => array()
        );
        return $ops;
    }

    /*
     * 数据库初始化
     * */
    public static function init($ar = array())
    {
        static $dns = array();
        if (!is_array($ar)) {
            $ar = array('conn' => $ar) + self::getarg();
        } else if (!$ar) {
            $ar = self::getarg();
        }
        self::$ops = $ar + self::$ops + self::getarg();
        $key = self::$ops['conn'];
        if (!isset($dns[$key])) {
            $ot = self::createPDO($key);
            $dns[$key] = $ot;
            return $ot;
        } else {
            $ot = $dns[$key];
            if (microtime(true) - $ot->start_time > 30) {
                unset($dns[$key]);
                $ot = self::createPDO($key);
                $dns[$key] = $ot;
                return $ot;
            } else {
                return $dns[$key];
            }
        }
    }

    private static function createPDO($key)
    {
        $conf = UConfPDO::getconf();
        if (!$conf[$key]) {
            exit('no find dns ' . $key);
        }
        $db = new PDO($conf[$key]['dns'], $conf[$key]['username'], $conf[$key]['passwd'], $conf[$key]['ops']);
        $ot = new self;
        $ot->db = $db;
        $ot->start_time = microtime(true);
        return $ot;
    }

    protected function _sql($ops, $d = 0)
    {
        if (is_string($ops)) {
            $this->sql = $ops;
            return true;
        }
        unset($ops['conn']);
        $ops = self::$ops = $ops + self::$ops + self::getarg();

        $sql = '';
        $ops['table'] = $this->_table($ops['table']);
        if ($d == 0) {
            $sql = 'SELECT ' . $ops['field'] . ' FROM ' . $ops['table'] .
                $this->_left($ops['left']) .
                $this->_where($ops['where']) .
                $this->_group($ops['group']) .
                $this->_order($ops['order']) .
                $this->_limit($ops['limit']);
        } elseif ($d == 1) {
            $sql = 'DELETE FROM ' . $ops['table'] . $this->_where($ops['where']);
        } elseif ($d == 2) {
            $sql = 'UPDATE ' . $ops['table'] . ' SET ' . $this->_data($ops['data']) . $this->_where($ops['where']);
        } elseif ($d == 3) {
            $ia = $this->_data($ops['data'], 1);
            $sql = 'INSERT INTO' . $ops['table'] . $ia[0] . ' VALUES ' . $ia[1];
        }
        $this->sql = $sql;
    }


    /*
     * array('tablename as dd','user.id = dd.uid');
     *
     * */
    protected function _left($ar)
    {
        $s = '';
        foreach ($ar as $k => $v) {
            $s .= ' LEFT JOIN ' . $k . ' on ' . $v;
        }
        return $s;
    }

    protected function _table($str)
    {
        $ar = explode(' ', trim($str));
        $tb = $ar[0];
        unset($ar[0]);
        return ' `' . $tb . '` ' . implode(' ', $ar);
    }

    public function _where($str = '')
    {
        $s = '';
        if (is_array($str) && $str) {
            $rr = array();
            foreach ($str as $k => $v) {
                if (is_array($v)) {
                    if ($v[0]) {
                        $rr[] = '(' . implode(' OR ', $v) . ')';
                    } else {
                        foreach ($v as $va => $vb) {
                            if (in_array(trim($va), array('in', 'is', 'not in'))) {
                                $rr[] = "{$k} {$va} {$vb}";
                            } else {
                                $rr[] = "{$k} {$va} '{$vb}'";
                            }
                        }

                    }
                } else {
                    $rr[] = "{$k} = '{$v}'";
                }
            }
            $s = ' WHERE ' . implode(' AND ', $rr);
        } else if ($str) {
            $s = ' WHERE ' . $str;
        }
        return $s;
    }

    protected function _order($str)
    {
        $s = '';
        if ($str) {
            $s = ' ORDER BY ' . $str;
        }
        return $s;
    }

    protected function _group($str)
    {
        $s = '';
        if ($str) {
            $s = ' GROUP BY ' . $str;
        }
        return $s;
    }

    protected function _limit($str)
    {
        $s = '';
        if ($str) {
            $s = ' LIMIT ' . $str;
        }
        return $s;
    }

    protected function _setsql($arg)
    {
        $trace = debug_backtrace();
        $arg['dns'] = self::$ops['conn'];
        $k = 1;
        if ($trace[1]['file'] == __FILE__) {
            $k = 3;
        }
        $arg['file'] = $trace[$k]['file'] . ':' . $trace[$k]['line'];
        self::$logs[] = $arg;
    }

    protected function _data($ar, $p = 0)
    {
        $r = array();
        if ($p == 1) {
            foreach ($ar as $k => $v) {
                $r[0][] = "`$k`";
                if (is_array($v)) {
                    $r[1][] = $v;
                } else {
                    $r[1][] = "'$v'";
                }
            }
            $r[0] = '(' . implode(',', $r[0]) . ')';
            if (is_array($r[1][0])) {
                $one = array();
                foreach ($ar as $key => $vr) {
                    foreach ($vr as $k => $v) {
                        $one[$k][] = "'{$ar[$key][$k]}'";
                    }
                }
                foreach ($one as $k => $v) {
                    $one[$k] = '(' . implode(',', $v) . ')';
                }
                $r[1] = implode(',', $one);
            } else {
                $r[1] = '(' . implode(',', $r[1]) . ')';
            }
            return $r;
        } else {
            foreach ($ar as $k => $v) {
                $r[] = "`$k` = '$v'";
            }
            return implode(',', $r);
        }
    }
}
