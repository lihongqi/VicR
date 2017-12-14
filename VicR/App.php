<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:48
 */
class App
{

    public static $start_time = 0;

    private static $config = [];

    /**
     * 自动加载
     * @param $path
     * @return mixed
     */
    public static function autoLoad($path)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        if (file_exists(VIC_APP_PATH . '/' . $path . '.php')) {
            return require_once(VIC_APP_PATH . '/' . $path . '.php');
        } else if (file_exists(VIC_CORE_PATH . '/' . $path . '.php')) {
            return require_once(VIC_CORE_PATH . '/' . $path . '.php');
        }
    }

    public static function loadRouter(){
        $key = md5(__FILE__);
        $time = filemtime(VIC_APP_PATH.'/Config/Router.php');
        $info  = Cache\File::get($key.$time);
        if($info){
            Router::$info  = $info;
            Router::$as_info = Cache\File::get($key.'_as'.$time);
        }else{
            self::config('Router');
            Cache\File::del($key.'*');
            Cache\File::set($key.$time,Router::$info,320000000);
            Cache\File::set($key.'_as'.$time,Router::$as_info,320000000);
        }
    }



    /**
     * 加载配置
     * @param $path
     * @return mixed
     */
    public static function config($path)
    {
        $res = Funcs::array_get(self::$config, $path);
        if (!$res) {
            $p = strpos($path, '.');
            if ($p !== false) {
                $name = substr($path, 0, $p);
                self::$config[$name] = require(VIC_APP_PATH . '/Config/' . $name . '.php');
            } else {
                self::$config[$path] = require(VIC_APP_PATH . '/Config/' . $path . '.php');
            }
            $res = Funcs::array_get(self::$config, $path);
        }
        return $res;
    }


    /**
     * @param string $fn
     * @param array $args
     * @return mixed
     */
    public static function call($fn, $args)
    {
        if (strpos($fn, '@') !== false) {
            $cl = explode('@', $fn);
            return call_user_func_array([new $cl[0], $cl[1]], $args);
        } else {
            return call_user_func_array($fn, $args);
        }
    }



}