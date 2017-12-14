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
        return 'hello ' . $name;
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