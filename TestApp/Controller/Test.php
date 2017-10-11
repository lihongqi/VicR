<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/10
 * Time: 下午4:02
 */

namespace Controller;


class Test
{
    public function hello($name)
    {
        return 'hello ' . $name;
    }

    public function hellos($name)
    {
        return 'hellos ' . $name;
    }

    public function helloAbc($name){
        echo __METHOD__."\n";
        return 'hello ' . $name;
    }

    public function xss($next)
    {
        echo __METHOD__."\n";
        return $next();
    }

    public static function xss2($next)
    {
        $args = \Request::getArgs();
        if(isset($args[0]) && $args[0] > 5){
            return 'id  不能大于 5';
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
        return 'id =  '.$id."\n";
    }

    public function getList()
    {
        echo __METHOD__."\n";
        return '哈哈哈 is getList '."\n";
    }
}