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
        self::$path = \App::config('Cache.file.path').'/';
        return self::$path;
    }

    private static function getFileName($key){
        $f=self::getPath().dirname(implode('/',str_split(substr($key,0,15),3)));
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
     * @param string $key
     * @param mixed $data
     * @param int $time
     * @return int
     */
    public static function set($key,$data,$time = 600){
        $f = self::getFileName($key);
        return file_put_contents($f,(time()+$time).serialize($data));
    }

    /**
     * @param $key
     */
    public static function del($key){
        $f = self::getFileName($key);
        $e = substr($f,-1,1);
        if($e == '*'){
            $base = substr($key,0,-1);
            foreach (self::getFiles(dirname($f)) as $k => $v){
                if(strpos($k,$base) === 0){
                    unlink($v);
                }
            }
        }else if(file_exists($f)){
            unlink($f);
        }
    }

    private static function getFiles($dir){
        $dir_iterator = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::LEAVES_ONLY);
        $files = [];
        foreach ($iterator as $file) {
            if(substr($file,-1,1) !== '.'){
                $files[$file->getfileName()] = $file->getPathName();
            }
        }
        unset($dir_iterator,$iterator);
        return $files;
    }



}