<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:45
 */
$time = microtime(true);
require_once __DIR__ . '/VicPhp/Main.php';
require_once __DIR__ . '/TestApp/index.php';

Router::get('/0','\App\User@index0');
Router::get('/name','\App\User@name');
Router::get('/name/0','\App\User@name0');
Router::get('/`^\w{3,5}$`/hers',['use' => '\User@nameHers','as' => 'hname']);
Router::get('/ahcd/hers',['use' => '\User@ahcd','as' => 'ahcd']);
Router::get('/name/h5/asdf/ewer','\App\User@Nooasdf');
Router::get('/', ['use' =>'VoyagerSettingsController@index', 'as' => 'index']);

Router::group([
    'as' => 'user',
    'namespace' => 'User',
    'prefix' => 'user',
    'middle' => ['middle@func1'],
    'cache' => ['use' => '\Cache@router','time' =>120]
],function (){
    Router::get('info',[
        'use' => 'SetTing@getUserInfo',
        'as' => 'getUserInfo'
    ]);
    Router::get('ienfo','Asf@fdse');
    Router::put('infeeo',[
        'use' => 'SetTing@getUserInfo',
        'as' => 'editUserInfo'
    ]);
    Router::get('addresses',[
        'use' => 'Addresses@get',
        'as' => 'getAddresses'
    ]);
    Router::post('addresses',[
        'use' => 'Addresses@create',
        'as' => 'createAddresses'
    ]);
    Router::group([
        'as' => 'coupon',
        'namespace' => 'Coupon',
        'prefix' => 'coupon'
    ],function (){
        Router::get('get',[
            'use' => 'CouponInfo@get',
            'as' => 'getCoupon'
        ]);
    });
});

Router::group(['as' => 'voyager.','namespace' => 'Voyager','prefix' => 'voyager'], function () {
    $namespacePrefix = '';
    Router::get('login', ['use' => $namespacePrefix.'VoyagerAuthController@login',     'as' => 'login']);
    Router::post('login', ['use' => $namespacePrefix.'VoyagerAuthController@postLogin', 'as' => 'postlogin']);
    Router::group(['middleware' => 'admin.user'], function () use ($namespacePrefix) {
        // Main Admin and Logout Router
        Router::get('/', ['use' => $namespacePrefix.'VoyagerController@index',   'as' => 'dashboard']);
        Router::post('logout', ['use' => $namespacePrefix.'VoyagerController@logout',  'as' => 'logout']);
        Router::post('upload', ['use' => $namespacePrefix.'VoyagerController@upload',  'as' => 'upload']);
        Router::get('profile', ['use' => $namespacePrefix.'VoyagerController@profile', 'as' => 'profile']);
        // Role Routes
        Router::resource('roles', $namespacePrefix.'VoyagerRoleController');
        // Menu Routes
        Router::group([
            'as'     => 'menus.',
            'prefix' => 'menus/{menu}',
        ], function () use ($namespacePrefix) {
            Router::get('builder', ['use' => $namespacePrefix.'VoyagerMenuController@builder',    'as' => 'builder']);
            Router::post('order', ['use' => $namespacePrefix.'VoyagerMenuController@order_item', 'as' => 'order']);
            Router::group([
                'as'     => 'item.',
                'prefix' => 'item',
            ], function () use ($namespacePrefix) {
                Router::delete('{id}', ['use' => $namespacePrefix.'VoyagerMenuController@delete_menu', 'as' => 'destroy']);
                Router::post('/', ['use' => $namespacePrefix.'VoyagerMenuController@add_item',    'as' => 'add']);
                Router::put('/', ['use' => $namespacePrefix.'VoyagerMenuController@update_item', 'as' => 'update']);
            });
        });
        // Settings
        Router::group([
            'as'     => 'settings.',
            'prefix' => 'settings',
        ], function () use ($namespacePrefix) {
            Router::get('/', ['use' => $namespacePrefix.'VoyagerSettingsController@index',        'as' => 'index']);
            Router::post('/', ['use' => $namespacePrefix.'VoyagerSettingsController@store',        'as' => 'store']);
            Router::put('/', ['use' => $namespacePrefix.'VoyagerSettingsController@update',       'as' => 'update']);
            Router::delete('{id}', ['use' => $namespacePrefix.'VoyagerSettingsController@delete',       'as' => 'delete']);
            Router::get('1000', ['use' => $namespacePrefix.'VoyagerSettingsController@k12312',      'as' => 'move_up']);
            Router::get('100/move_up', ['use' => $namespacePrefix.'VoyagerSettingsController@move_up100',      'as' => 'move_up']);
            Router::get('{id}/move_up', ['use' => $namespacePrefix.'VoyagerSettingsController@move_up',      'as' => 'move_up']);
            Router::get('{id}/move_down', ['use' => $namespacePrefix.'VoyagerSettingsController@move_down',    'as' => 'move_down']);
            Router::get('{id}/delete_value', ['use' => $namespacePrefix.'VoyagerSettingsController@delete_value', 'as' => 'delete_value']);
        });
        // Admin Media
        Router::group([
            'as'     => 'media.',
            'prefix' => 'media',
        ], function () use ($namespacePrefix) {
            Router::get('/', ['use' => $namespacePrefix.'VoyagerMediaController@index',              'as' => 'index']);
            Router::post('files', ['use' => $namespacePrefix.'VoyagerMediaController@files',              'as' => 'files']);
            Router::post('new_folder', ['use' => $namespacePrefix.'VoyagerMediaController@new_folder',         'as' => 'new_folder']);
            Router::post('delete_file_folder', ['use' => $namespacePrefix.'VoyagerMediaController@delete_file_folder', 'as' => 'delete_file_folder']);
            Router::post('directories', ['use' => $namespacePrefix.'VoyagerMediaController@get_all_dirs',       'as' => 'get_all_dirs']);
            Router::post('move_file', ['use' => $namespacePrefix.'VoyagerMediaController@move_file',          'as' => 'move_file']);
            Router::post('rename_file', ['use' => $namespacePrefix.'VoyagerMediaController@rename_file',        'as' => 'rename_file']);
            Router::post('upload', ['use' => $namespacePrefix.'VoyagerMediaController@upload',             'as' => 'upload']);
            Router::post('remove', ['use' => $namespacePrefix.'VoyagerMediaController@remove',             'as' => 'remove']);
            Router::post('crop', ['use' => $namespacePrefix.'VoyagerMediaController@crop',             'as' => 'crop']);
        });
        // Database Routes
        Router::group([
            'as'     => 'database.bread.',
            'prefix' => 'database',
        ], function () use ($namespacePrefix) {
            Router::get('{table}/bread/create', ['use' => $namespacePrefix.'VoyagerDatabaseController@addBread',     'as' => 'create']);
            Router::post('bread', ['use' => $namespacePrefix.'VoyagerDatabaseController@storeBread',   'as' => 'store']);
            Router::get('{table}/bread/edit', ['use' => $namespacePrefix.'VoyagerDatabaseController@addEditBread', 'as' => 'edit']);
            Router::put('bread/{id}', ['use' => $namespacePrefix.'VoyagerDatabaseController@updateBread',  'as' => 'update']);
            Router::delete('bread/{id}', ['use' => $namespacePrefix.'VoyagerDatabaseController@deleteBread',  'as' => 'delete']);
            Router::post('bread/relationship', ['use' => $namespacePrefix.'VoyagerDatabaseController@addRelationship',  'as' => 'relationship']);
            Router::get('bread/delete_relationship/{id}', ['use' => $namespacePrefix.'VoyagerDatabaseController@deleteRelationship',  'as' => 'delete_relationship']);
        });
        // Compass Routes
        Router::group([
            'as'     => 'compass.',
            'prefix' => 'compass',
        ], function () use ($namespacePrefix) {
            Router::get('/', ['use' => $namespacePrefix.'VoyagerCompassController@index',  'as' => 'index']);
            Router::post('/', ['use' => $namespacePrefix.'VoyagerCompassController@index',  'as' => 'post']);
        });
        Router::resource('database', $namespacePrefix.'VoyagerDatabaseController');
    });
});

//echo '<hr>';
//print_r(Router::$info);
//print_r(Router::$as_info);
//
//Router::$info = json_decode(file_get_contents(__DIR__.'/RunCache/router.json'),true);
//Router::$as_info = json_decode(file_get_contents(__DIR__.'/RunCache/routeras.json'),true);
echo "\n";
echo (microtime(true) - $time) * 1000;
echo "\n";
$r = Router::getExecInfo();
print_r($r);
print_r(Router::$args);
//echo "\n";
echo (microtime(true) - $time) * 1000;
//echo "\n";
exit;

// 主要逻辑
$meet = function($name){
    echo "nice to meet you, $name \n";
    return '$meet';
};

// 前置中间件
$hello = function($handler){
    return function($name)use($handler){
        echo "hello ".$name.", may I have your name\n";
        $name = 'Lucy';
        return $handler($name);
    };
};

// 前置中间件
$weather = function($handler){
    return function($name)use($handler){
        echo 'what a day'."\n";
        return $handler($name); // weather_return_handler行
    };
};

// 后置中间件
$dinner = function($handler){
    return function($name)use($handler){
        $return = $handler($name);
        $name = 'Lucy';
        echo "OK, $name. Will you have dinner with me?\n";
        return $return;
    };
};
// 中间件栈
$stack = [];

// 打包
function prepare($handler, $stack){
    foreach(array_reverse($stack) as $key => $fn){
         echo $key,"\n";
        $handler = $fn($handler); // 记为iterator行
    }
    return $handler;
}
// 入栈
$stack['dinner'] = $dinner;
$stack['weather'] = $weather;
$stack['hello'] = $hello;


// 把所有逻辑打包成一个闭包（closure）
$run = prepare($meet, $stack);

$r = $run('beauty'); // 记为run_prepare
echo $r;