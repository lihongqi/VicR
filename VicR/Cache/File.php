<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/9
 * Time: 下午3:50
 */

namespace Cache;


class File
{
    private static $path = '';

    private static function getPath(){
        if(self::$path){
            return self::$path;
        }
        self::$path = \App::Config('cache.file.path');
        return self::$path;
    }

    private static function getFileName($key){
        $f=self::getPath().dirname(implode('/',str_split($key,2)));
        if(!is_dir($f)){
            mkdir($f,0755,true);
        }
        return $f.'/'.$key;
    }

    /**
     * 获取缓存信息
     * @param $key
     * @return bool|mixed
     */
    public static function get($key){
        $f = self::getFileName($key);
        if(file_exists($f)){
            $n=file_get_contents($f);
            $t1=substr($n,0,10);
            if($t1 < time()){
                unlink($f);
                return false;
            }else{
                return unserialize(substr($n,10));
            }
        }else{
            return false;
        }
    }

    /**
     * @param static $key
     * @param mixed $data
     * @param int $time
     * @return int
     */
    public static function set($key,$data,$time = 600){
        $f = self::getFileName($key);
        return file_put_contents($f,(time()+$time).serialize($data));
    }

}