<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/10
 * Time: 下午4:02
 */

namespace Controller;


use Cache\File;

class Test
{
    public function hello($name)
    {
        return 'hello ' . $name."\n";
    }

    public function name()
    {
        $key = 'abcde';
        echo __METHOD__;
    }

    public function hellos($name)
    {
        return 'hellos .. ' . $name."\n";
    }

    public function helloAbc($name){
        echo __METHOD__."\n";
        return 'hello ' . $name;
    }

    public function xss($next,$cl)
    {
        echo __METHOD__." exec {$cl} \n";
        return $next();
    }

    public static function xss2($next)
    {
        $args = \Router::$args;
        if(isset($args[0]) && $args[0] > 5){
            return 'id  不能大于 5'."\n";
        }else{
            echo __METHOD__."\n";
            return $next();
        }
    }

    public function csrf($next)
    {
        echo __METHOD__."\n";
        return $next();
    }

    public function topic($id)
    {
        echo __METHOD__."\n";
        return '不能大于 id =  '.$id."\n";
    }

    public function getList()
    {
        echo "\n".(microtime(true) - \App::$start_time) * 1000 ."\n";
        echo __METHOD__."\n";
        return '哈哈哈 is getList '."\n";
    }
}