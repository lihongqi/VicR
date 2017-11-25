<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/10
 * Time: 下午4:02
 */

namespace Controller;


use Cache\File;
use Model\KeyVal;
use Model\Tag;

class Test
{
    public function hello($name)
    {
        return 'hello ' . $name."\n";
    }


    public function name($i){
        echo (microtime(true) - \App::$start_time) * 1000 , PHP_EOL;
        echo $i;
    }




    public function csrf($next)
    {
        echo __METHOD__."\n";
        return $next();
    }

}