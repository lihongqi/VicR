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
    public static $table = '';

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
            'table' => static::$table,
            'where' => $data,
            'limit' => 1
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function findAll($data = []){
        return Db::init(static::$connection)->findAll($data + [
            'table' => static::$table,
            'limit' => 10
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function delete($data){
        return Db::init(static::$connection)->delete([
            'table' => static::$table,
            'where' => $data
        ]);
    }

    /**
     * @param array $data
     * @return string
     */
    public static function insert($data){
        return Db::init(static::$connection)->insert([
            'table' => static::$table,
            'data' => $data
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function update($data){
        return Db::init(static::$connection)->update($data + [
            'table' => static::$table,
        ]);
    }

}