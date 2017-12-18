<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/9
 * Time: 下午4:20
 */

Router::group([
    'namespace' => 'Controller'
], function () {

    Router::get('/', 'Test@index');

    Router::get('/drm', 'Test@drm');

    //打印出路由缓存信息
    Router::get('/router', 'Test@router');

    // {name} 通用匹配
    Router::get('/hello/{name}', 'Test@hello');

    //{id} 匹配数字
    Router::get('/blog/{id}', 'Test@blog');

    // 缓存 10秒
    Router::get('/cache', [
        'use' => 'Test@cache',
        'cache' => [
            'time' => 10 //10s
        ]
    ]);

    //中间件csrf
    Router::group([
        'middle' => [\Middle\Safe::class . '@csrfSetSign'],
    ], function () {
        Router::get('/login', 'Test@login');
    });

    Router::post('/login', [
        'use' => 'Test@into',
        'as' => 'login.into',
        'middle' => [\Middle\Safe::class . '@csrfVerifySign']
    ]);


});