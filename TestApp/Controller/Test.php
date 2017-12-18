<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/10
 * Time: 下午4:02
 */

namespace Controller;


use Cache\File;
use Model\Blog;
use Model\KeyVal;
use Model\Tag;

class Test
{
    public function hello($name)
    {
        return 'hello ' . $name;
    }

    public function drm(){
        Blog::find([
            'id' => 1,
            'star' => 0
        ]);
        // SELECT * FROM  `blogs` WHERE id = '1' AND star = '0' LIMIT 1

        $time = time() - 1186400;
        Blog::find([
            'id' => 1,
            ['add_time', '>=', $time],
            ['add_time', '<', $time + 1186400],
            'star' => 0
        ]);
        // SELECT * FROM  `blogs` WHERE add_time >= '1513416708' AND add_time < '1513503108' AND id = '1' AND star = '0' LIMIT 1


        Blog::with('author');
        Blog::find([
            ['id','in',[1,2]],
            ['add_time', '>=', $time],
            ['add_time', '<', $time + 1186400],
            'star' => 0
        ]);
        //SELECT * FROM  `blogs` WHERE id in ('1','2') AND add_time >= '1513416708' AND add_time < '1513503108' AND star = '0' LIMIT 1
        //SELECT id,name FROM  `author` WHERE id in ('3')

        Blog::find([
            ['id'=>1,'star' => 0],
            ['add_time', '>=', $time],
            ['add_time', '<', $time + 1186400]
        ]);
        //SELECT * FROM  `blogs` WHERE (id = '1' OR star = '0') AND add_time >= '1513416708' AND add_time < '1513503108' LIMIT 1

    }

    public function router()
    {
        return json_encode([\Router::$info,\Router::$as_info]);
    }

    public function index()
    {
        return 'Welcome to use';
    }

    public function blog($id)
    {
        return __METHOD__ . ' arg:' . $id;
    }

    public function cache()
    {
        return date('Y-m-d H:i:s');
    }

    public function login()
    {
        return \Response::tpl('Test/Login', ['title' => 'login']);
    }

    public function into()
    {

    }


}