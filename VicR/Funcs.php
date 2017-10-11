<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/9
 * Time: 下午4:01
 */
class Funcs
{
    /**
     * @param array $arr
     * @param $key
     * @return mixed|null
     */
    public static function array_get($arr, $key)
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $v) {
                if (isset($arr[$v])) {
                    $arr = $arr[$v];
                } else {
                    return null;
                }
            }
            return $arr;
        } else if (isset($arr[$key])) {
            return $arr[$key];
        } else {
            return null;
        }
    }


    /**
     * @param array $arr
     * @param array $keys
     * @return mixed|null
     */
    public static function array_get_not_null($arr, $keys)
    {
        foreach ($keys as $v) {
            if (self::array_get($arr, $v) !== null) {
                return self::array_get($arr, $v);
            }
        }
        return null;
    }

//    /**
//     * @param array $arr
//     * @return mixed|null
//     */
//    public static function return_true($arr)
//    {
//        foreach ($arr as $v) {
//            if ($v) {
//                return $v;
//            }
//        }
//        return null;
//    }
}