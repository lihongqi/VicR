<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:54
 */
define('VIC_CORE_PATH',__DIR__);
require_once VIC_CORE_PATH.'/App.php';
spl_autoload_register('App::autoLoad');

App::Config('router');

try{
    Router::exec();
}catch (Exception $e){
    echo $e->getMessage();
}

