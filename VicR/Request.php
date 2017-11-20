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
    public static function ip()
    {
        return Funcs::array_get_not_null($_SERVER, ['REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR']);
    }


    /**
     * @param $name
     * @return mixed|null
     */
    public static function server($name){
        return Funcs::array_get($_SERVER, $name);
    }

    /**
     * @return mixed|null
     */
    public static function userAgent(){
        return self::server('HTTP_USER_AGENT');
    }

    /**
     * @return string
     */
    public static function uri(){
        return Router::$uri;
    }

    /**
     * request unique id
     * @return string
     */
    public static function id()
    {
        static $id = null;
        if (!$id) {
            $id = Funcs::uuid();
        }
        return $id;
    }

    /**
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public static function get($key,$default = null)
    {
        $r = Funcs::array_get($_GET, $key);
        if(!$r){
            $r = $default;
        }
        return $r;
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
    public static function res($key)
    {
        return Funcs::array_get($_REQUEST, $key);
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
    public static function json(){
        return json_decode(self::input(),true);
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
    public static function method()
    {
        return strtolower(self::server('REQUEST_METHOD'));
    }

    /**
     * @return bool
     */
    public static function isAjax(){
        if(self::server('HTTP_X_REQUESTED_WITH')){
            return true;
        }else{
            return false;
        }
    }


}