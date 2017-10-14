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
        } else {
            exit('没有找到文件:' . $path);
        }
    }

    public static function loadRouter(){
        $key = md5(__FILE__);
        $time = filemtime(VIC_APP_PATH.'/Config/router.php');
        $info  = Cache\File::get($key.$time);
        if($info){
            Router::$info  = $info;
            Router::$as_info = Cache\File::get('router_as'.$time);
        }else{
            self::config('Router');
            Cache\File::del($key.'*');
            Cache\File::set($key.$time,Router::$info,36000000);
            Cache\File::set($key.'_as'.$time,Router::$as_info,36000000);
        }
    }

    /**
     * 加载配置
     * @param $path
     * @return mixed
     */
    public static function config($path)
    {
        if (!isset(self::$config[$path])) {
            $p = strpos($path, '.');
            if ($p !== false) {
                $arr = require(VIC_APP_PATH . '/Config/' . substr($path, 0, $p) . '.php');
                self::$config[$path] = Funcs::array_get($arr, substr($path, $p + 1));
            } else {
                self::$config[$path] = require(VIC_APP_PATH . '/Config/' . $path . '.php');
            }
        }
        return self::$config[$path];
    }


}