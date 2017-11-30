<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/14
 * Time: 下午8:34
 */

namespace Drm;


class Base
{
    CONST TABLE = '';

    public static $connection = 'default';
    
    /**
     * @param $field
     * @param $sign
     * @param $val
     * @return Db
     */
    public static function where($field, $sign, $val){
        return Db::init(static::$connection)->where($field,$sign,$val);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function find($data){
        return Db::init(static::$connection)->find([
            'table' => static::TABLE,
            'where' => $data,
            'limit' => 1
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function findById($id){
        return self::find(['id' => $id]);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function findAll($data = []){
        return Db::init(static::$connection)->findAll($data + [
            'table' => static::TABLE,
            'limit' => 10
        ]);
    }


    /**
     * @param array $data
     * @return array
     */
    public static function findAllAndCount($data = []){
        $arr = self::findAll($data);
        if(!isset($data['where'])){
            $data['where'] = [];
        }
        $rows = self::count($data['where']);
        return [$arr,$rows];
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function count($data){
        $res = Db::init(static::$connection)->find([
            'table' => static::TABLE,
            'field' => 'count(*) as rows',
            'where' => $data
        ]);
        return $res['rows'];
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function delete($data){
        return Db::init(static::$connection)->delete([
            'table' => static::TABLE,
            'where' => $data
        ]);
    }

    /**
     * @param array $data
     * @return string
     */
    public static function insert($data){
        return Db::init(static::$connection)->insert([
            'table' => static::TABLE,
            'data' => $data
        ]);
    }

    /**
     * @param array $data
     * @param array $where
     * @return mixed
     */
    public static function update($data,$where = []){
        if(!$where){
            $where = ['id' => $data['id']];
            unset($data['id']);
        }
        return Db::init(static::$connection)->update([
            'table' => static::TABLE,
            'where' => $where,
            'data' => $data
        ]);
    }

    /**
     * @return array
     */
    public static function getField(){
        $arr =  Db::init(static::$connection)->getDbField();
        return $arr[static::TABLE];
    }

    /**
     * @param array $data
     * @param bool $is_allow_empty
     */
    public static function filterData($data,$is_allow_empty = false){
        $fields = self::getField();
        $r = [];
        foreach ($data as $k => $v){
            if(isset($fields[$k]) && ($is_allow_empty || $data[$k] != '')){
                $r[$k] = $v;
            }
        }
        return $r;
    }

}