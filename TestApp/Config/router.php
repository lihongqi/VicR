<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/9
 * Time: 下午4:20
 */
Router::get('/{id}',[
    'use' => '\\Controller\\Test@hellos',
    'middle' => [
        '\\Controller\\Test@xss','\\Controller\\Test::xss2'
    ]
]);
Router::get('/{name}/abc','\\Controller\\Test@helloAbc');
Router::get('/{name}','\\Controller\\Test@hello');
Router::group([
    'middle' => ['\\Controller\\Test@xss','\\Controller\\Test::xss2'],
],function (){
    Router::group([
        'middle' => ['\\Controller\\Test@csrf'],
    ],function (){
        Router::get('/topic','\\Controller\\Test@getList');
        Router::get('/topic/{id}','\\Controller\\Test@topic');
    });
});