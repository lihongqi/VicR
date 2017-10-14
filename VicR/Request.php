<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/10
 * Time: 下午1:46
 */
class Request
{

    /**
     * @return string|null
     */
    public static function getIp()
    {
        return Funcs::array_get_not_null($_SERVER, ['REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR']);
    }

    /**
     * request unique id
     * @return string
     */
    public static function requestId()
    {
        static $id = null;
        if (!$id) {
            $id = uniqid();
        }
        return $id;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return Funcs::array_get($_GET, $key);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public static function post($key)
    {
        return Funcs::array_get($_POST, $key);
    }


    /**
     * @param $key
     * @return mixed|null
     */
    public static function cookie($key)
    {
        return Funcs::array_get($_COOKIE, $key);
    }

    /**
     * @return string
     */
    public static function input()
    {
        return file_get_contents('php://input');
    }

    /**
     * @return array
     */
    public static function file()
    {
        $files = [];
        foreach ($_FILES as $name => $fs) {
            $keys = array_keys($fs);
            if (is_array($fs[$keys[0]])) {
                foreach ($keys as $k => $v) {
                    foreach ($fs[$v] as $name => $val) {
                        $files[$name][$v] = $val;
                    }
                }
            } else {
                $files[$name] = $fs;
            }
        }
        return $files;
    }

    /**
     * @return string
     */
    public static function requestMethod()
    {
        return strtolower(Funcs::array_get($_SERVER, 'REQUEST_METHOD'));
    }

}