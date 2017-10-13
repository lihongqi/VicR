<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:45
 */
define('VIC_APP_PATH',__DIR__);
require_once VIC_APP_PATH . '/../VicR/VicR.php';

App::$start_time = microtime(true);

App::loadRouter();

try{
    Router::exec();
}catch (Exception $e){
    echo $e->getMessage();
}


