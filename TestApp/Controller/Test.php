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

    public function name($id = 1)
    {

        $res = Tag::find([
            'id' => $id,
            'name' => \Router::$paths[3]
        ]);
        var_dump($res);
        echo '<hr>';
        $res = Tag::findAll([
            'order' => 'id asc',
            'limit' => '5,10'
        ]);
        var_dump($res);
        echo '<hr>';
        $res = Tag::update([
            'where' => [
                'id' => 12
            ],
            'data' => [
                'name' => 'update 12'
            ]
        ]);
        var_dump($res);
        echo '<hr>';
        $res = Tag::delete([
            'id' => 15
        ]);
        var_dump($res);
        echo '<hr>';
        $res = KeyVal::insert([
            'k' => 'aa'.rand(1,1000),
            'v' => 'v\'v'.time()
        ]);
        var_dump($res);
        echo '<hr>';
        $res = KeyVal::insert([
            ['k' => 'kss1'.rand(1,1000),'v' => 'vss1'.time()],
            ['k' => 'kss2'.rand(1,1000),'v' => 'vss2'.time()],
            ['k' => 'kss3'.rand(1,1000),'v' => 'vss3'.time()],
            ['k' => 'kss4'.rand(1,1000),'v' => 'vss4'.time()],
            ['k' => 'kss5'.rand(1,1000),'v' => 'vss5'.time()],
        ]);
        var_dump($res);
        echo '<hr>';
        $res = KeyVal::findAll(['limit' => '6,100']);
        var_dump($res);
        echo '<hr>';


//        $res = Tag::update([
//            'where' => [
//                'id' => 12
//            ],
//            'data' => [
//                'name' => 'update 12'
//            ]
//        ]);
//        var_dump($res);
//        echo '<hr>';



    }


    public function helloAbc($name){
        echo __METHOD__."\n";
        return 'hello ' . $name;
    }



    public function csrf($next)
    {
        echo __METHOD__."\n";
        return $next();
    }

}