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

    /**
     * @param array $data
     * @return mixed
     */
    public static function find($data){
        return Db::init()->find([
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
        return Db::init()->findAll($data + [
            'table' => static::$table,
            'limit' => 10
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function delete($data){
        return Db::init()->delete([
            'table' => static::$table,
            'where' => $data
        ]);
    }

    /**
     * @param array $data
     * @return string
     */
    public static function insert($data){
        return Db::init()->insert([
            'table' => static::$table,
            'data' => $data
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function update($data){
        return Db::init()->update($data + [
            'table' => static::$table,
        ]);
    }

}